<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Deck</title>
    <!-- Add Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        @foreach ($cards as $card)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="{{ asset('images/' . $card['image']) }}" class="card-img-top" alt="{{ $card['title'] }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $card['title'] }}</h5>
                        <p class="card-text">{{ $card['description'] }}</p>
                        <a href="#" class="btn btn-primary">SCARICA IL CATALOGO</a>
                        <a href="#" class="btn btn-secondary">RICHIEDI INFORMAZIONI</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
