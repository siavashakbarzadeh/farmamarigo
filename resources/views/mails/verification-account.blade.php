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

<p>Vi preghiamo di confermare la vostra richiesta di iscrizione premendo il seguente link:</p>

<a href="{{ $url }}" target="_blank">{{ $url }}</a>
<div class="card text-center">
    <div class="card-header">
        <h2>Vi preghiamo di confermare la vostra richiesta di iscrizione premendo il seguente link:</h2>
    </div>
    <div class="card-body">
        <a href="{{ $url }}" target="_blank">{{ $url }}</a>

    </div>
    <div class="card-footer text-body-secondary"> Per qualsiasi informazione, ci contatti via <a href='https://dev.marigo.collaudo.biz/contact'>Pagina dei contatti</a>
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
