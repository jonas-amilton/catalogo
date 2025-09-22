@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h2>Produtos (Admin)</h2>
        @livewire('admin.product-list')
    </div>
@endsection
