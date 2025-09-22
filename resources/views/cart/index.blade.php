@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div id="cart-container" class="max-w-4xl mt-6 mx-auto py-10 px-6 sm:px-8 lg:px-10 bg-white rounded shadow">
            <h1 class="py-3 text-3xl font-bold mb-8">Pedidos</h1>

            @if (empty($cart['items']) || count($cart['items']) === 0)
                <p class="text-gray-700">Seu carrinho está vazio.</p>
                <a href="{{ url('/products') }}" class="mt-4 inline-block text-indigo-600 hover:underline pb-3">Voltar para a
                    loja</a>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-300">
                            <th class="py-2">Produto</th>
                            <th class="py-2">Preço Unitário</th>
                            <th class="py-2">Quantidade</th>
                            <th class="py-2">Subtotal</th>
                            <th class="py-2">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart['items'] as $productId => $item)
                            <tr class="border-b border-gray-200" data-product-id="{{ $productId }}"
                                data-unit-price="{{ $item['unit_price'] }}">
                                <td class="py-3">{{ $item['name'] }}</td>
                                <td class="py-3 unit-price">R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                                <td class="py-3">
                                    <input type="number" min="1" value="{{ $item['quantity'] }}"
                                        data-product-id="{{ $productId }}"
                                        class="quantity-input w-20 border border-gray-300 rounded px-2 py-1" />
                                </td>
                                <td class="py-3 item-subtotal">R$
                                    {{ number_format($item['unit_price'] * $item['quantity'], 2, ',', '.') }}</td>
                                <td class="py-3">
                                    <button type="button" data-product-id="{{ $productId }}"
                                        class="remove-item text-red-600 hover:underline">Remover</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p class="pt-2">
                    <span id="cart-total" class="text-xl font-semibold">Total: R$
                        {{ number_format($cart['total'], 2, ',', '.') }}</span>
                </p>

                <div class="mt-6 flex justify-end items-center space-x-6 pb-3">
                    <!-- botão agora chama AJAX -->
                    <button id="checkout-btn" type="button"
                        class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">
                        Finalizar Compra
                    </button>
                </div>
            @endif
        </div>
    </div>
@endsection

<!-- Toastr/SweetAlert/jQuery imports (se já estiverem no layout, remova daqui) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    <script>
        $(function() {
            const csrfToken = '{{ csrf_token() }}';

            function formatCurrency(value) {
                return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',');
            }

            function parseCurrencyToFloat(text) {
                if (!text) return 0;
                text = String(text).replace('R$', '').trim();
                text = text.replace(/\./g, '').replace(',', '.');
                return parseFloat(text) || 0;
            }

            function recalcTotalFromDOM() {
                let total = 0;
                $('tbody tr').each(function() {
                    const $row = $(this);
                    const unitPrice = parseFloat($row.data('unit-price')) || 0;
                    const qty = parseInt($row.find('.quantity-input').val(), 10) || 0;
                    total += unitPrice * qty;
                });
                return total;
            }

            function computeBadgeFromDOM() {
                let count = 0;
                $('tbody tr').each(function() {
                    const qty = parseInt($(this).find('.quantity-input').val(), 10) || 0;
                    count += qty;
                });
                return count;
            }

            function updateCartCount(count) {
                if (!count || count <= 0) {
                    $('.cart-count-badge').remove();
                    return;
                }
                if ($('.cart-count-badge').length === 0) {
                    // tenta inserir em containers conhecidos
                    $('.cart-count-badge-container').each(function() {
                        if (!$(this).find('.cart-count-badge').length) {
                            $(this).append(
                                `<span class="cart-count-badge ml-1 inline-block bg-red-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full">${count}</span>`
                            );
                        }
                    });
                } else {
                    $('.cart-count-badge').text(count);
                }
            }

            $(document).off('change', '.quantity-input');
            $(document).off('click', '.remove-item');
            $(document).off('click', '#checkout-btn');

            $(document).on('change', '.quantity-input', function() {
                const $input = $(this);
                const productId = $input.data('product-id');
                let quantity = parseInt($input.val(), 10);

                if (isNaN(quantity) || quantity < 1) {
                    toastr.error('Quantidade inválida');
                    $input.val(1);
                    quantity = 1;
                }

                const $row = $input.closest('tr');
                const unitPrice = parseFloat($row.data('unit-price')) || 0;

                $input.prop('disabled', true).addClass('opacity-60 cursor-not-allowed');

                $.ajax({
                    url: '/cart/' + productId,
                    method: 'PUT',
                    data: {
                        quantity: quantity,
                        _token: csrfToken
                    },
                    success: function(response) {
                        const subtotal = unitPrice * quantity;
                        $row.find('.item-subtotal').text(formatCurrency(subtotal));

                        if (response.cart && typeof response.cart.total !== 'undefined') {
                            $('#cart-total').text('Total: ' + formatCurrency(response.cart
                                .total));
                        } else if (typeof response.total !== 'undefined') {
                            $('#cart-total').text('Total: ' + formatCurrency(response.total));
                        } else {
                            const totalFromDOM = recalcTotalFromDOM();
                            $('#cart-total').text('Total: ' + formatCurrency(totalFromDOM));
                        }

                        const newCount = response.items_count ?? (response.cart?.count ?? null);
                        if (newCount !== null) {
                            updateCartCount(newCount);
                        } else {
                            updateCartCount(computeBadgeFromDOM());
                        }

                        toastr.success('Quantidade atualizada');
                    },
                    error: function(xhr) {
                        let msg = 'Erro ao atualizar o carrinho';
                        if (xhr.status === 422 && xhr.responseJSON?.message) msg = xhr
                            .responseJSON.message;
                        toastr.error(msg);
                    },
                    complete: function() {
                        $input.prop('disabled', false).removeClass(
                            'opacity-60 cursor-not-allowed');
                    }
                });
            });

            $(document).on('click', '.remove-item', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const productId = $btn.data('product-id');
                const $row = $btn.closest('tr');

                if ($btn.prop('disabled')) return;

                Swal.fire({
                    title: 'Remover item',
                    text: 'Tem certeza que deseja remover este item do carrinho?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, remover',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $btn.prop('disabled', true).addClass('opacity-60 cursor-not-allowed');

                    $.ajax({
                        url: '/cart/' + productId,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            $row.remove();

                            if (response.cart && typeof response.cart.total !==
                                'undefined') {
                                $('#cart-total').text('Total: ' + formatCurrency(
                                    response.cart.total));
                            } else if (typeof response.total !== 'undefined') {
                                $('#cart-total').text('Total: ' + formatCurrency(
                                    response.total));
                            } else {
                                const totalFromDOM = recalcTotalFromDOM();
                                $('#cart-total').text('Total: ' + formatCurrency(
                                    totalFromDOM));
                            }

                            const newCount = response.items_count ?? (response.cart
                                ?.count ?? null);
                            if (newCount !== null) {
                                updateCartCount(newCount);
                            } else {
                                updateCartCount(computeBadgeFromDOM());
                            }

                            const remainingRows = $('tbody tr').length;
                            if (!remainingRows) {
                                $('#cart-container').html(`
                                    <h1 class="py-3 text-3xl font-bold mb-8">Pedidos</h1>
                                    <p class="text-gray-700">Seu carrinho está vazio.</p>
                                    <a href="{{ url('/products') }}" class="mt-4 inline-block text-indigo-600 hover:underline pb-3">Voltar para a loja</a>
                                `);
                            }

                            toastr.success('Item removido do carrinho');
                        },
                        error: function(xhr) {
                            let msg = 'Erro ao remover item';
                            if (xhr.status === 422 && xhr.responseJSON?.message) msg =
                                xhr.responseJSON.message;
                            toastr.error(msg);
                        },
                        complete: function() {
                            $btn.prop('disabled', false).removeClass(
                                'opacity-60 cursor-not-allowed');
                        }
                    });
                });
            });

            $(document).on('click', '#checkout-btn', function(e) {
                e.preventDefault();

                const $btn = $(this);
                if ($btn.prop('disabled')) return;

                Swal.fire({
                    title: 'Finalizar compra',
                    text: 'Deseja confirmar o pedido e finalizar a compra?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, confirmar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $btn.prop('disabled', true).addClass('opacity-60 cursor-not-allowed').text(
                        'Processando...');

                    $.ajax({
                        url: '{{ route('orders.store') }}',
                        method: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            toastr.success(response.message ||
                                'Pedido realizado com sucesso');

                            $('.cart-count-badge').remove();

                            if (response.order_id) {
                                window.location.href = '/orders/' + response.order_id;
                                return;
                            }

                            window.location.href = '/orders';
                        },
                        error: function(xhr) {
                            let msg = 'Erro ao finalizar a compra';
                            if (xhr.status === 422 && xhr.responseJSON?.message) msg =
                                xhr.responseJSON.message;
                            if (xhr.responseJSON?.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat()
                                    .join('<br>');
                            }
                            toastr.error(msg);
                        },
                        complete: function() {
                            $btn.prop('disabled', false).removeClass(
                                'opacity-60 cursor-not-allowed').text(
                                'Finalizar Compra');
                        }
                    });
                });
            });
        });
    </script>
@endpush
