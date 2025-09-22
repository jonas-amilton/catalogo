<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrderList extends Component
{
    use WithPagination;

    public $perPage = 12;
    protected $paginationTheme = 'tailwind';
    protected $updatesQueryString = ['page'];

    public function mount()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Gate::denies('viewOwnList', Order::class)) {
            abort(403);
        }
    }

    public function render()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.order-list', compact('orders'));
    }
}