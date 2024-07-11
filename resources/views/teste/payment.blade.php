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
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Pagamento</h1>
        <form id="payment-form" class="needs-validation" novalidate>

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
                <input type="text" class="form-control" id="value" name="value" value="215.00" required>
                <div class="invalid-feedback">Por favor, insira o valor.</div>
            </div>

            <div class="form-group">
                <label for="description">Descrição:</label>
                <input type="text" class="form-control" id="description" name="description" value="echo dot v3"
                    required>
                <div class="invalid-feedback">Por favor, insira a descrição.</div>
            </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var url = "{{ $url }}";

            document.getElementById('payment-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);


                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text()) // Alterado para text() para depuração
                    .then(responseText => {
                        console.log('Resposta bruta da API:', responseText);

                        // Tente analisar o JSON manualmente
                        try {

                            const data = JSON.parse(responseText);
                            alert('Pagamento enviado com sucesso!' + data['tipo']);
                            var imgElement = document.getElementById('image');
                            imgElement.src = "data:image/png;base64," + data['qrCode'];
                            imgElement.style.display = 'block'; // Exibe a imagem
                            var divPix = document.getElementById('pix');
                            divPix.style.display = 'block';


                        } catch (error) {
                            console.error('Erro ao analisar JSON:', error);
                            alert(
                                'Erro ao analisar a resposta da API. Verifique o console para mais detalhes.'
                                );
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao enviar pagamento:', error);
                        alert('Erro ao enviar pagamento. Verifique o console para mais detalhes.');
                    });










            });
        });
    </script>
</body>

</html>
