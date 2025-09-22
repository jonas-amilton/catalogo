@php
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];

    $statusLabels = [
        'pending' => 'Pendente',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
    ];
@endphp

<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white shadow rounded p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Meus Pedidos</h2>
            <div class="text-sm text-gray-600">Total: <span class="font-medium">{{ $orders->total() }}</span></div>
        </div>

        @if ($orders->count())
            <div class="overflow-x-auto">
                <table class="w-full table-auto text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="px-4 py-2 text-sm text-gray-700">Código</th>
                            <th class="px-4 py-2 text-sm text-gray-700">Itens</th>
                            <th class="px-4 py-2 text-sm text-gray-700">Total</th>
                            <th class="px-4 py-2 text-sm text-gray-700">Status</th>
                            <th class="px-4 py-2 text-sm text-gray-700">Criado em</th>
                            <th class="px-4 py-2 text-sm text-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-sm break-words">{{ $order->code }}</td>
                                <td class="px-4 py-3 text-sm">{{ $order->items->sum('quantity') }} item(s)</td>
                                <td class="px-4 py-3 text-sm">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span
                                        class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $statusClasses[$order->status] ?? '' }}">
                                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="text-indigo-600 hover:underline">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <p class="text-gray-600">Você ainda não fez nenhum pedido.</p>
        @endif
    </div>
</div>
