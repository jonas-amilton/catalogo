@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h2>Pedidos (Admin)</h2>
        @livewire('admin.order-list')
    </div>
@endsection
