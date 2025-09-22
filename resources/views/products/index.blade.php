@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-6">Listagem de Produtos</h1>
        @livewire('product-index')
    </div>
@endsection
