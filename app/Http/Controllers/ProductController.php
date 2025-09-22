<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return view('products.show', compact('product'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        return view('products.create');
    }

    public function store(ProductStoreRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->validated();
        $slug = Str::slug($data['name']);
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        $data['slug'] = $slug;

        $product = Product::create($data);

        return redirect()->route('products.show', $product->slug)->with('success', 'Produto criado');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validated();

        if (isset($data['name']) && $data['name'] !== $product->name) {
            $slug = Str::slug($data['name']);
            $original = $slug;
            $i = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $original . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        $product->update($data);

        return redirect()->route('products.show', $product->slug)->with('success', 'Produto atualizado');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto deletado');
    }
}