<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductList extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $perPage = 12;
    protected $listeners = ['productSaved' => '$refresh'];

    public function applySearch()
    {
        $this->resetPage();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);

        $product->delete();

        session()->flash('success', 'Produto deletado');
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name');

        $products = $query->paginate($this->perPage);

        return view('livewire.admin.product-list', compact('products'));
    }

    public function mount()
    {
        $this->authorize('viewAny', Product::class);
    }
}