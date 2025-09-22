<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 12;
    public $onlyActive = true;
    public $page = 1;

    protected $queryString = ['search', 'onlyActive', 'page'];

    protected $paginationTheme = 'tailwind';

    public function applySearch()
    {
        $this->resetPage();
    }

    public function updatingOnlyActive()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->onlyActive, fn($q) => $q->where('active', true))
            ->orderBy('name');

        $products = $query->paginate($this->perPage);

        return view('livewire.product-index', ['products' => $products]);
    }
}