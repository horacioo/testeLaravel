@php
    $url = route('integrandoComPlataforma');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Asaas</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        #dadosCartaoDeCreditoForm {
            border: 1px solid rgb(0, 33, 132);
            background-color: white;
            padding: 2vw;
            border-radius: 0.7vw;
            margin-bottom:2vw;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Pagamento</h1>






        <form method="post" id="payment-form" action={{ route('obrigado') }} class="needs-validation">
            @csrf
            <div class="form-group">
                <label for="billingType">Forma de Pagamento:</label>
                <select class="form-control" id="billingType" name="billingType" required>
                    <option value="">Selecione uma forma de pagamento</option>
                    <option value="BOLETO">Boleto</option>
                    <option value="CREDIT_CARD">Cartão de Crédito</option>
                    <option value="PIX">PIX</option>
                </select>
                <div class="invalid-feedback">Por favor, selecione uma forma de pagamento.</div>
            </div>

            <div class="form-group">
                <label for="value">Valor:</label>
                <input type="text" class="form-control" id="value" name="value" value="R$215,00" required>
                <div class="invalid-feedback">Por favor, insira o valor.</div>
            </div>

            <div class="form-group">
                <label for="description">Descrição:</label>
                <input type="text" class="form-control" id="description" name="description" value="echo dot v3"
                    required>
                <div class="invalid-feedback">Por favor, insira a descrição.</div>
            </div>



            <!------------------------------------------------------------------->
            <div id="dadosCartaoDeCreditoForm">
     
              @if(session('text')!= null) <h2 style="color:red">{{session('text')}}</h2> @endif

                <h2>Dados do Cartão de Crédito</h2>
                <div class="form-group">
                    <label for="description">NomeDoTituar:</label>
                    <input type="text" class="form-control" id="description" name="cartaoTit" required>
                </div>
                <div class="form-group">
                    <label for="description">Numero do Cartão:</label>
                    <input type="text" class="form-control" id="description" name="cartaoNum" required>
                </div>


                <div class="form-group">
                    <label for="description">Expira no mês de :</label>
                    <select class="form-control" name="ExpireMonth" required>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                </div>



                <div class="form-group">
                    <label for="description">Ano de Expiração:</label>
                    <select class="form-control" name="ExpireYear" required>
                        {{ $currentYear = date('y') }}
                        @for ($i = -19; $i < 20; $i++)
                            <option value="{{ $currentYear + $i }}">{{ $currentYear + $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Código De Segurança:</label>
                    <input type="text" class="form-control" id="description" name="cartaoCcv" required>
                </div>
            </div>
            <!------------------------------------------------------------------->





            <button type="submit" class="btn btn-primary btn-block">Pagar</button>
        </form>





    </div>

    <div id="pix" style="display: none;">
        <img id="image" src="" alt="QR Code Image">
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>




    <!---<script src="{{ asset('js/app.js') }}"></script>-->
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
        integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script>

        jQuery("document").ready(function() {
               @if(session('text')=== null) jQuery('#dadosCartaoDeCreditoForm').hide(); @endif
        });


        $('#billingType').change(function() {
            if ($(this).val() === 'CREDIT_CARD') {
                jQuery('#dadosCartaoDeCreditoForm').show();
            }
        });

        $('#billingType').change(function() {
            if ($(this).val() != 'CREDIT_CARD') {
                jQuery('#dadosCartaoDeCreditoForm').hide();
            }
        });
    </script>

</body>

</html>
