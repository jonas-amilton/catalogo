@extends('layouts.app')

@section('content')
    <div class="mt-6 max-w-xl mx-auto py-8 px-4 sm:px-6 lg:px-8 bg-white rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">{{ $product->name }}</h2>

        <p class="text-gray-700 mb-2">Preço: <span
                class="font-medium">R${{ number_format($product->price, 2, ',', '.') }}</span></p>
        <p class="text-gray-700 mb-6">Estoque: <span id="product-stock" class="font-medium">{{ $product->stock }}</span></p>

        @guest
            <div class="mb-4 max-w-xs">
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                <input type="number" id="qty" min="1" max="{{ $product->stock }}" value="1"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            <button id="add-to-cart" data-product-id="{{ $product->id }}" @if ($product->stock <= 0) disabled @endif
                class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition">
                Adicionar ao Carrinho
            </button>
        @endguest
    </div>
@endsection

<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@push('scripts')
    <script>
        $(function() {
            const cartLinkHref = '{{ route('cart.index') }}';

            function updateCartCount(count) {
                if (!count || count <= 0) {
                    document.querySelectorAll('.cart-count-badge').forEach(el => el.remove());
                    return;
                }
                let updated = false;
                document.querySelectorAll('.cart-count-badge').forEach(el => {
                    el.textContent = count;
                    updated = true;
                });
                if (updated) return;
                document.querySelectorAll('.cart-count-badge-container').forEach(container => {
                    if (container.querySelector('.cart-count-badge')) return;
                    const span = document.createElement('span');
                    span.className =
                        'cart-count-badge ml-1 inline-block bg-red-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full';
                    span.textContent = count;
                    container.appendChild(span);
                });
                if (!document.querySelector('.cart-count-badge')) {
                    const a = document.querySelector(`a[href="${cartLinkHref}"]`);
                    if (a) {
                        const span = document.createElement('span');
                        span.className =
                            'cart-count-badge ml-1 inline-block bg-red-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full';
                        span.textContent = count;
                        a.appendChild(span);
                    }
                }
            }

            $(document).off('click', '#add-to-cart');

            $(document).on('click', '#add-to-cart', function(e) {
                e.preventDefault();

                const btn = $(this);
                const productId = btn.data('product-id');
                const quantity = parseInt($('#qty').val()) || 1;

                if (quantity < 1) {
                    if (window.toastr) toastr.error('Quantidade inválida');
                    else alert('Quantidade inválida');
                    return;
                }

                if (btn.prop('disabled')) return;
                btn.prop('disabled', true).addClass('opacity-60 cursor-not-allowed');

                $.ajax({
                    url: '{{ route('cart.add') }}',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const newCount = response.items_count ?? (response.cart?.count ?? null);
                        if (newCount !== null) updateCartCount(newCount);

                        if (window.toastr) toastr.success('Produto adicionado ao carrinho!');
                        else alert('Produto adicionado ao carrinho!');

                        if (response.cart && response.cart.items && response.cart.items[
                                productId]) {
                            const newStock = {{ $product->stock ?? 0 }} - response.cart.items[
                                productId].quantity;
                            $('#product-stock').text(newStock);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Erro ao adicionar ao carrinho';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                            .message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (window.toastr) toastr.error(msg);
                        else alert(msg);
                    },
                    complete: function() {
                        btn.prop('disabled', false).removeClass(
                            'opacity-60 cursor-not-allowed');
                    }
                });
            });

            $(document).off('change', '.quantity-input');
            $(document).on('change', '.quantity-input', function() {
                const productId = $(this).data('product-id');
                const quantity = parseInt($(this).val());

                if (quantity < 1) {
                    alert('Quantidade inválida');
                    return;
                }

                $.ajax({
                    url: '/cart/' + productId,
                    method: 'PUT',
                    data: {
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const newCount = response.cart?.count ?? null;
                        if (newCount !== null) updateCartCount(newCount);
                        location.reload();
                    },
                    error: function() {
                        alert('Erro ao atualizar o carrinho');
                    }
                });
            });

            $(document).off('click', '.remove-item');
            $(document).on('click', '.remove-item', function() {
                if (!confirm('Remover item?')) return;
                const productId = $(this).data('product-id');

                $.ajax({
                    url: '/cart/' + productId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const newCount = response.cart?.count ?? null;
                        if (newCount !== null) updateCartCount(newCount);
                        location.reload();
                    },
                    error: function() {
                        alert('Erro ao remover item');
                    }
                });
            });

        });
    </script>
@endpush
