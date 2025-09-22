<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
        <form wire:submit.prevent="applySearch" class="w-full sm:w-1/2 flex">
            <input wire:model.defer="search" type="text" placeholder="Procurar produtos..."
                class="flex-grow border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />

            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-r-md hover:bg-indigo-700 transition">
                Buscar
            </button>
        </form>
        <label class="inline-flex items-center space-x-2">
            <input wire:model="onlyActive" type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" />
            <span class="text-gray-700 select-none">Somente ativos</span>
        </label>
    </div>

    @if ($products->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <h5 class="text-lg font-semibold mb-2">{{ $product->name }}</h5>
                    <p class="text-gray-700 mb-1">Preço: R${{ number_format($product->price, 2, ',', '.') }}</p>
                    <p class="text-gray-700 mb-2">Estoque: {{ $product->stock }}</p>
                    @if (!$product->active)
                        <span
                            class="inline-block bg-gray-300 text-gray-700 text-xs px-2 py-1 rounded mb-2">Inactive</span>
                    @endif
                    <div class="mt-auto">
                        <a href="{{ route('products.show', $product->slug) }}"
                            class="inline-block px-3 py-1 border border-indigo-600 text-indigo-600 rounded hover:bg-indigo-600 hover:text-white transition">
                            Detalhe do Produto
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $products->links('pagination::tailwind') }}
        </div>
    @else
        <div class="p-4 bg-blue-100 text-blue-800 rounded">
            Nenhum produto encontrado.
        </div>
    @endif
</div>
