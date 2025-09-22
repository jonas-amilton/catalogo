<div>
    <div class="flex justify-between items-center mb-4">
        <form wire:submit.prevent="applySearch" class="w-full sm:w-1/2 flex">
            <input wire:model.defer="search" type="text" placeholder="Procurar produtos..."
                class="flex-grow border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />

            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-r-md hover:bg-indigo-700 transition">
                Buscar
            </button>
        </form>


        <a href="{{ route('admin.products.create') }}"
            class="ml-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
            Novo Produto
        </a>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($product->active)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Sim</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600">Não</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.products.edit', $product) }}"
                                class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            <button wire:click="deleteProduct({{ $product->id }})"
                                onclick="return confirm('Confirm delete?')"
                                class="text-red-600 hover:text-red-900">Remover</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum produto encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links('pagination::tailwind') }}
    </div>
</div>
