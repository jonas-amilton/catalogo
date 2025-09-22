<div>
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="max-w-lg space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input id="name" wire:model.defer="name" type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" />
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Preço</label>
                <input id="price" wire:model.defer="price" type="number" step="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-500 @enderror" />
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Estoque</label>
                <input id="stock" wire:model.defer="stock" type="number"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('stock') border-red-500 @enderror" />
                @error('stock')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center mt-6">
                <input id="active" wire:model="active" type="checkbox"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                <label for="active" class="ml-2 block text-sm text-gray-700">Ativo</label>
            </div>
        </div>

        <div class="flex space-x-4">
            <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Salvar
            </button>
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
