<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add(CartAddRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'Estoque insuficiente'], 422);
        }

        $cart = session()->get('cart', ['items' => [], 'total' => 0, 'count' => 0]);

        if (isset($cart['items'][$product->id])) {
            $cart['items'][$product->id]['quantity'] += $request->quantity;
        } else {
            $cart['items'][$product->id] = [
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'name' => $product->name,
            ];
        }

        $this->recalculateCart($cart);

        session()->put('cart', $cart);

        return response()->json([
            'total' => $cart['total'],
            'items_count' => $cart['count'],
            'cart' => $cart,
        ]);
    }

    public function index()
    {
        $cart = session()->get('cart', ['items' => [], 'total' => 0, 'count' => 0]);

        return view('cart.index', compact('cart'));
    }

    public function show()
    {
        $cart = session()->get('cart', ['items' => [], 'total' => 0, 'count' => 0]);
        return response()->json($cart);
    }

    public function update(Request $request, $productId)
    {
        $qty = (int) $request->input('quantity', 1);
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', ['items' => [], 'total' => 0, 'count' => 0]);

        if (!isset($cart['items'][$productId])) {
            return response()->json(['message' => 'Item não está no carrinho'], 404);
        }

        if ($qty > $product->stock) {
            return response()->json(['message' => 'Estoque insuficiente'], 422);
        }

        if ($qty <= 0) {
            unset($cart['items'][$productId]);
        } else {
            $cart['items'][$productId]['quantity'] = $qty;
        }

        $this->recalculateCart($cart);

        session()->put('cart', $cart);

        return response()->json(['total' => $cart['total'], 'items_count' => $cart['count'], 'cart' => $cart]);
    }

    public function remove($productId)
    {
        $cart = session()->get('cart', ['items' => [], 'total' => 0, 'count' => 0]);

        if (isset($cart['items'][$productId])) {
            unset($cart['items'][$productId]);
            $this->recalculateCart($cart);
            session()->put('cart', $cart);
        }

        return response()->json(['total' => $cart['total'], 'items_count' => $cart['count'], 'cart' => $cart]);
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', ['items' => [], 'total' => 0, 'count' => 0]);

        if ($cart['count'] === 0) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        session()->forget('cart');

        return response()->json(['message' => 'Checkout finalizado com sucesso']);
    }

    protected function recalculateCart(array &$cart)
    {
        $total = 0;
        $count = 0;
        foreach ($cart['items'] as $item) {
            $total += $item['unit_price'] * $item['quantity'];
            $count += $item['quantity'];
        }
        $cart['total'] = $total;
        $cart['count'] = $count;
    }
}