<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Obrigado</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>

    <div class="container text-center mt-5">
        <div class="card">
            <div class="card-header">
                <h1>Obrigado!</h1>


                @if ($tipo == 'pix')
                    <p>o tipo de pagamento escolhido foi: "<strong>{{ $tipo }}</strong>"</p>
                    <p>copie e cole no app de seu banco para realizar o pagamento:
                        <strong>{{ $text }}</strong>
                    </p>
                    <p>
                        <img src="data:image/png;base64, {{ $imagem }}">
                    </p>
                    
                @elseif($tipo == 'boleto')
                    <a href="{{ $link }}" class="btn btn-primary" target='_blank'>
                        Clique aqui e acesse seu boleto
                    </a>

                @elseif($tipo == 'cartao_credito')
                    <a href="{{ $link }}" class="btn btn-primary" target='_blank'>
                        Clique aqui e pague com seu cartão de crédito
                    </a>
                @endif

            </div>
            <div class="card-body">
                <p class="card-text">Sua submissão foi recebida com sucesso.</p>
                <a href="{{ route('pagamento') }}" class="btn btn-primary">Voltar ao Início</a>
            </div>
        </div>
    </div>
    <!---<script src="{{ asset('js/app.js') }}"></script>-->
</body>

</html>
