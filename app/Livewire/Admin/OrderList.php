<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderList extends Component
{
    use WithPagination, AuthorizesRequests;

    public $perPage = 12;

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $this->authorize('update', $order);

        if (!in_array($status, ['pending', 'paid', 'cancelled'])) {
            session()->flash('error', 'Status inválido');
            return;
        }

        $order->update(['status' => $status]);
        session()->flash('success', 'Status do pedido atualizado');
    }

    public function render()
    {
        $orders = Order::with('user')->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.admin.order-list', compact('orders'));
    }
}