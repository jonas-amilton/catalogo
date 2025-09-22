@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h2>Editar Produto</h2>
        @livewire('admin.product-form', ['productId' => $product->id])
    </div>
@endsection
