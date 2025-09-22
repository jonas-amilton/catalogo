@component('mail::message')
    # Confirmação do Pedido

    Thank you for your order!

    Código do Pedido: {{ $order->code }}

    @component('mail::table')
        | Produto | Quantidade | Preço unitario |
        | ------------- |:--------:| ----------:|
        @foreach ($order->items as $item)
            | {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->unit_price, 2) }} |
        @endforeach
    @endcomponent

    **Total:** ${{ number_format($order->total, 2) }}

    Obrigado,<br>
    {{ config('app.name') }}
@endcomponent
