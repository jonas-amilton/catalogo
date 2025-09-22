@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h2>Criar Produto</h2>
        @livewire('admin.product-form')
    </div>
@endsection
