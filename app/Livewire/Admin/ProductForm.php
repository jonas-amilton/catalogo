<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductForm extends Component
{
    use AuthorizesRequests;

    public $productId;
    public $name;
    public $price;
    public $stock;
    public $active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'active' => 'nullable|boolean',
        ];
    }

    public function mount($productId = null)
    {
        $this->productId = $productId;

        if ($productId) {
            $product = Product::findOrFail($productId);
            $this->authorize('update', $product);

            $this->name = $product->name;
            $this->price = $product->price;
            $this->stock = $product->stock;
            $this->active = (bool) $product->active;
        } else {
            $this->authorize('create', Product::class);
        }
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $this->authorize('update', $product);

            if ($data['name'] !== $product->name) {
                $slug = Str::slug($data['name']);
                $original = $slug;
                $i = 1;
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $original . '-' . $i++;
                }
                $data['slug'] = $slug;
            }

            $product->update($data);
            session()->flash('success', 'Produto atualizado');
        } else {
            $slug = Str::slug($data['name']);
            $original = $slug;
            $i = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $i++;
            }
            $data['slug'] = $slug;

            Product::create($data);
            session()->flash('success', 'Produto criado');
        }

        $this->dispatch('productSaved');

        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.admin.product-form');
    }
}