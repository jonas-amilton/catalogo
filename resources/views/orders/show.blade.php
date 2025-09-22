@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto mt-8 bg-white rounded shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Pedido {{ $order->code }}</h2>

            <p class="text-sm text-gray-600 mb-4">Status:
                <span class="font-semibold">
                    {{ ucfirst($order->status) }}
                </span>
            </p>

            <div class="mb-6">
                <h3 class="font-semibold mb-2">Itens</h3>
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço Unit.</th>
                            <th>Qtd</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-t">
                                <td class="py-2">{{ $item->product->name ?? 'Produto excluído' }}</td>
                                <td class="py-2">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="py-2">{{ $item->quantity }}</td>
                                <td class="py-2">R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-right">
                <p class="text-lg font-semibold">Total: R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500">Criado em: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
@endsection
