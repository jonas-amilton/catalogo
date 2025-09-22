@php
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'paid' => 'bg-blue-100 text-blue-800',
    ];

    $statusLabels = [
        'pending' => 'Pendente',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
        'paid' => 'Pago',
    ];
@endphp

<div>
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $order->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ optional($order->user)->email ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-0.5 rounded text-xs font-semibold {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="updateStatus({{ $order->id }}, 'paid')"
                                class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 transition">
                                Marcar como Pago
                            </button>
                            <button wire:click="updateStatus({{ $order->id }}, 'cancelled')"
                                class="px-3 py-1 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                Cancelar
                            </button>
                            <a href="{{ route('orders.show', $order) }}"
                                class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 transition">
                                Visualizar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum pedido encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links('pagination::tailwind') }}
    </div>
</div>
