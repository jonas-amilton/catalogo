<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Jobs\SendOrderConfirmationJob;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $this->authorize('create', Order::class);

        $cart = session()->get('cart');

        if (empty($cart) || empty($cart['items'])) {
            return response()->json(['message' => 'Carrinho vazio'], 422);
        }

        try {
            $order = DB::transaction(function () use ($cart) {
                $calculatedTotal = 0;
                $itemsData = [];

                foreach ($cart['items'] as $productId => $item) {
                    $product = Product::lockForUpdate()->find($productId);

                    if (!$product) {
                        throw new \Exception("Produto {$productId} não encontrado");
                    }

                    $qty = (int) $item['quantity'];
                    if ($product->stock < $qty) {
                        throw new \Exception("Estoque insuficiente para {$product->name}");
                    }

                    $unitPrice = (float) $product->price;
                    $subtotal = $unitPrice * $qty;
                    $calculatedTotal += $subtotal;

                    $itemsData[] = [
                        'product' => $product,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];
                }

                $order = Order::create([
                    'code' => Str::uuid(),
                    'user_id' => auth()->id(),
                    'total' => $calculatedTotal,
                    'status' => 'pending',
                ]);

                foreach ($itemsData as $it) {
                    $product = $it['product'];

                    $product->decrement('stock', $it['quantity']);

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $it['quantity'],
                        'unit_price' => $it['unit_price'],
                    ]);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao criar pedido: ' . $e->getMessage()], 500);
        }

        if (config('queue.default') === 'sync') {
            SendOrderConfirmationJob::dispatchSync($order);
        } else {
            SendOrderConfirmationJob::dispatch($order);
        }

        session()->forget('cart');

        return response()->json([
            'message' => 'Pedido realizado com sucesso',
            'order_id' => $order->id
        ]);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return view('orders.show', compact('order'));
    }

    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(12);
        return view('admin.orders.index', compact('orders'));
    }

    public function myOrders()
    {
        return view('orders.index');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $status = $request->input('status');
        if (!in_array($status, ['pending', 'paid', 'cancelled'])) {
            return redirect()->back()->withErrors('Status inválido');
        }

        $order->status = $status;
        $order->save();

        return redirect()->back()->with('success', 'Status do pedido atualizado');
    }
}