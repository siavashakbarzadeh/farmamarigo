<!doctype html>
<html lang="en">
<head>
    <title>Completamento dell'ordine #10000{{$order->id}}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
<div class="card text-center">
    <div class="card-header">
        <h2>La ringraziamo per il suo ordine!</h2>
    </div>
    <div class="card-body">
        <h5 class="card-title">Gentile {{ $order->user->name }}</h5>
        <p class="card-text">il suo ordine #10000{{$order->id}} è confermato ed è pronto per la spedizione.</p>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Codice</th>
                <th>{{ __('Product') }}</th>
                <th>{{ __('Amount') }}</th>
                <th style="width: 100px">{{ __('Importo') }}</th>
                <th class="price text-right">{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->products as $orderProduct)
                @php
                    $product = get_products([
                    'condition' => [
                        'ec_products.id' => $orderProduct->product_id,
                    ],
                    'take'   => 1,
                    'select' => [
                        'ec_products.id',
                        'ec_products.images',
                        'ec_products.name',
                        'ec_products.price',
                        'ec_products.sale_price',
                        'ec_products.sale_type',
                        'ec_products.start_date',
                        'ec_products.end_date',
                        'ec_products.sku',
                        'ec_products.is_variation',
                        'ec_products.status',
                        'ec_products.order',
                        'ec_products.created_at',
                    ],
                ]);

                @endphp
                <tr>
                    <td class="align-middle">{{ $product->sku }}</td>
                    <td class="align-middle">
                        {{ $orderProduct->product_name }}
                        @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                            @foreach($orderProduct->options as $option)
                                @if (!empty($option['key']) && !empty($option['value']))
                                    <p class="mb-0"><small>{{ $option['key'] }}:
                                            <strong> {{ $option['value'] }}</strong></small></p>
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td class="align-middle">{{ $orderProduct->amount_format }}</td>
                    <td class="align-middle">{{ $orderProduct->qty }}</td>
                    <td class="money text-right align-middle">
                        <strong>
                            {{ $orderProduct->total_format }}
                        </strong>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <a href="https://marigopharma.marigo.collaudo.biz/customer/orders/view/{{$order->id}}" style='display: block;margin-block-end: 1em' class="btn btn-primary">Vedere
            ordine</a>
        <br>
    </div>
    <div class="card-footer text-body-secondary"> Per qualsiasi informazione, ci contatti via <a href='https://marigopharma.marigo.collaudo.biz/contact'>Pagina dei contatti</a>
    </div>
    <div class="row m-0">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-4">
                    <div class="logo logo-width-1 wow fadeIn   animated" style="visibility: visible;">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('storage/logo.jpg') }}" width="130" style="display:block;margin-block-start:2em;margin-block-end:-10px;" alt="Marigolab">
                        </a>
                    </div>
                </div>
                <div class="col-4">
                    <h4 class="wow fadeIn   animated" style="visibility: visible;">
                        <strong class="d-inline-block">MARIGO ITALIA SRL</strong>
                    </h4>
                    <p class="wow fadeIn   animated" style="visibility: visible;">
                        <strong class="d-inline-block">indirizzo:</strong> Via Bagnulo, 168, 80063 Piano di Sorrento NA
                    </p>
                    <p class="wow fadeIn   animated" style="visibility: visible;">
                        <strong class="d-inline-block">Telefono:</strong> +39 0815344611
                    </p>
                    <p class="wow fadeIn   animated" style="visibility: visible;">
                        <strong class="d-inline-block">E-mail:</strong> info@marigoitalia.it
                    </p>
                    <p class="wow fadeIn   animated" style="visibility: visible;">
                        <strong class="d-inline-block">Partita IVA:</strong> 07500660639
                    </p>
                </div>
                <div class="col-4">                    <h4 class="mb-10 mt-20 fw-600 text-grey-4 wow fadeIn   animated" style="visibility: visible;">Seguici</h4>
                    <div class="mobile-social-icon wow fadeIn  mb-sm-5 mb-md-0  animated" style="visibility: visible;">
                        <a href="https://www.facebook.com/" title="Facebook" style="background-color: #3B5999;color: #ffffff;padding: 8px;text-decoration:none; border: 1px solid #3B5999;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/" title="Instagram" style="background-color: #E1306C;color: #ffffff;padding: 8px;text-decoration:none; border: 1px solid #E1306C;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/" title="Linkedin" style="background-color: #007BB6;color: #ffffff;padding: 8px;text-decoration:none; border: 1px solid #007BB6;">
                            <i class="fab fa-linkedin"></i>
                        </a>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
</body>
</html>




