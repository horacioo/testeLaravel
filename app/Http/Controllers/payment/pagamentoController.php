<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class pagamentoController extends Controller
{
    private $minhaChave = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwODQyNTk6OiRhYWNoXzg1MTkyNmI3LWUzZGQtNDk1Zi05MDEwLTEzYjBiNWIwY2IwMg==';
    private $cliente = "cus_000006095618";
    private $url = "https://sandbox.asaas.com/api/v3/payments/";
    private $response;
    private $retornoDadosProcessados;
    private $valor;
    private $avaliacaoValor;
    private $CartaoDeCredito;
    private $parcelas;
    private $minValue = 5.00;
    private $maxParcelas = 12;


    public function index()
    {
        return view('teste.payment');
    }

    /*************************************************/
    /*************************************************/




    public function conclusao(Request $request)
    {
        $dados = $request->all();


        $this->CartaoDeCredito =
            array(
                "holderName" => $request->cartaoTit,
                "number" =>  $request->cartaoNum,
                "expiryMonth" =>  $request->ExpireMonth,
                "expiryYear" => $request->ExpireYear,
                "ccv" =>  $request->cartaoCcv,
            );


        $retorno = $this->Api($dados);
        if ($this->avaliacaoValor == 1) :

            return view('teste.obrigado')->with('tipo', $this->retornoDadosProcessados['tipo'])
                ->with('text', $this->retornoDadosProcessados['codigoExtenso'])
                ->with('imagem', $this->retornoDadosProcessados['imagem'])
                ->with('link', $this->retornoDadosProcessados['linkBoleto']);
        else :
            return redirect()->route('pagamento')->with('text', $this->retornoDadosProcessados['codigoExtenso']);
        endif;
    }















    private function TrataValor($x)
    {
        if (is_float($x)) {
            $x = str_replace('.', ',', $x);
        }
        $valor = str_replace("R$", "", $x);
        $valor2 = str_replace(".", "", $valor);
        $valor3 = str_replace(",", ".", $valor2);
        $valor3 = number_format($valor3, 2, '.', '');
        $this->valor = $valor3;
        $valorFloat = (float) $valor3;
        if ($valorFloat > 5.00) {
            $this->avaliacaoValor = 1;
        } else {
            $this->avaliacaoValor = 0;
        }
        return $valorFloat;
    }
    /*************************************************/
    /*************************************************/
















    /***Aqui eu vou acesaar a api do Asaas***/
    public function Api($dados)
    {
        // Verifica se os dados foram recebidos corretamente
        $data = $dados;
        $data['dueDate'] = date('Y-m-d', strtotime('+3 days'));
        $data['customer'] = $this->cliente;
        $data['value'] = $this->TrataValor($dados['value']);
        $data['description'] = "api de pagamento";
        $this->parcelas = $dados['parcelas'];
        $this->TrataValor($data['value']);

        $dataToSend = [
            'customer' => 'cus_000006095618',
            'billingType' => 'BOLETO',
            'dueDate' => '2024-07-13',
            'value' => 100.00,
            'description' => 'Serviço de consultoria'
        ];
        $requiredParams = ['customer', 'billingType', 'dueDate', 'value', 'description'];
        foreach ($requiredParams as $param) {
            if (!isset($data[$param])) {
                return json_encode(['error' => 'Parâmetro obrigatório ausente: ' . $param]);
                //return;
            }
        }

        $dataToSend = array_merge($dataToSend, $data);

        $url = $this->url;
        $apiKey = $this->minhaChave;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'access_token: ' . $apiKey . '',
            'User-Agent: NomeDoSeuApp/1.0'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataToSend));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        // Verifica o código HTTP da resposta
        if ($httpCode >= 400) {
            return json_encode(['error' => 'Erro ao processar a requisição: ' . $response]);
        } else {
            $this->response = json_decode($response, true);
        }

        curl_close($ch);

        $funcao = $data['billingType'];
        $this->$funcao();
    }



















    /***************************************************************/
    /***************************************************************/
    /***************************************************************/
    public function CREDIT_CARD()
    {
        $apiKey = $this->minhaChave;
        $apiUrl = 'https://sandbox.asaas.com/api/v3/creditCard/tokenize';
        $cliente = $this->cliente;
        $data = array(
            "creditCard" => array(
                "number" => str_replace(' ', '', $this->CartaoDeCredito['number']),
                "holderName" => $this->CartaoDeCredito['holderName'],
                "expiryMonth" => intval($this->CartaoDeCredito['expiryMonth']),
                "expiryYear" => intval($this->CartaoDeCredito['expiryYear']),
                "ccv" => $this->CartaoDeCredito['ccv'],
            ), 'customer' => $this->cliente
        );


        $headers = array(
            'Content-Type: application/json',
            'access_token: ' . $apiKey,
            'User-Agent: NomeDoSeuApp/1.0'
        );
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Adiciona os dados do cartão

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            die('Erro na requisição: ' . $error);
        }
        curl_close($ch);




        $responseData = json_decode($response, true); // Decodifica a resposta

        if (isset($responseData['creditCardToken'])) {
            $this->avaliacaoValor = 1;
            $this->retornoDadosProcessados = [
                "tipo" => "cartao_credito",
                "codigoExtenso" => $responseData['creditCardToken'],
                "imagem" => "#",
                "linkBoleto" => $this->response['invoiceUrl']
            ];

            $data['creditCard']['token'] = $responseData['creditCardToken'];
            $this->retornoDadosProcessados['creditCard']['token'] = $responseData['creditCardToken'];
            $this->ExecutaPagamentoComCartao();
        } else {
            $this->avaliacaoValor = 0;
            $this->retornoDadosProcessados = [
                "tipo" => "cartao_credito",
                "codigoExtenso" => $responseData['errors'][0]['description'],
                "imagem" => "#",
                "linkBoleto" => $this->response['invoiceUrl']
            ];
        }
    }

    /***************************************************************/
    /***************************************************************/
    /***************************************************************/




    /***************************************************************/
    /***************************************************************/
    /***************************************************************/
    private function ExecutaPagamentoComCartao()
    {

        $valorDaParcela = $this->CalculoParcela();

        $data_pgto = date('Y-m-d', strtotime('+3 days'));
        $curl = curl_init();
        $data = [
            "billingType" => "CREDIT_CARD",
            "customer" => $this->cliente,
            "value" => $this->valor,
            "installmentCount" => 3,
            "dueDate" => $data_pgto,
            "installmentValue" => $this->retornoDadosProcessados['ValorDeCadaParcelas'],
            "creditCardToken" => $this->retornoDadosProcessados['creditCard']['token']//$dados['token']
        ];


        curl_setopt_array($curl, [
            CURLOPT_URL => "https://sandbox.asaas.com/api/v3/payments/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "access_token: " . $this->minhaChave . "",
                "content-type: application/json",
                "User-Agent: NomeDoSeuApp/1.0"
            ],
        ]);


       

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        } else {
            $data = json_decode($response, true);
            $this->retornoDadosProcessados = [
                "tipo" => "cartao_credito",
                "codigoExtenso" => "Seu Pagamento foi Realizado com sucesso",
                "imagem" => "#",
                "linkBoleto" => "#"
            ];
        }

        curl_close($curl);
    }

    /***************************************************************/
    /***************************************************************/
    /***************************************************************/






    public function BOLETO()
    {
        $this->retornoDadosProcessados = [
            "tipo" => "boleto",
            "codigoExtenso" => "1234567890",
            "imagem" => "#",
            "linkBoleto" => $this->response['bankSlipUrl']
        ];
    }












    public function PIX()
    {
        $retorno = null;
        $idPix = $this->response['id'] ?? null;

        if (!$idPix) {
            return 'ID PIX não encontrado na resposta';
        }

        $url = $this->url . $idPix . '/pixQrCode';
        $apiKey = $this->minhaChave;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'access_token: ' . $apiKey,
            'User-Agent: NomeDoSeuApp/1.0'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            return 'Erro ao obter o QR Code: ' . $response;
        } else {
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'Erro na decodificação do JSON: ' . json_last_error_msg();
            }
            if (is_array($data) && isset($data['encodedImage'])) {
                // Retorna um array PHP

                $this->retornoDadosProcessados = [
                    "tipo" => "pix",
                    "codigoExtenso" => $this->criaCodigoPix(),
                    "imagem" => $data['encodedImage'],
                    "linkBoleto" => "#"
                ];
            } else {
                return 'Response não é um JSON válido ou não contém a chave encodedImage';
            }
        }
    }























    public function criaCodigoPix()
    {
        return  $this->geraPix("28630597852", $idTx = '', $valor = $this->valor);
    }





    function formataCampo($id, $valor)
    {
        return $id . str_pad(strlen($valor), 2, '0', STR_PAD_LEFT) . $valor;
    }
    function calculaCRC16($dados)
    {
        $resultado = 0xFFFF;
        for ($i = 0; $i < strlen($dados); $i++) {
            $resultado ^= (ord($dados[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($resultado & 0x8000) {
                    $resultado = ($resultado << 1) ^ 0x1021;
                } else {
                    $resultado <<= 1;
                }
                $resultado &= 0xFFFF;
            }
        }
        return strtoupper(str_pad(dechex($resultado), 4, '0', STR_PAD_LEFT));
    }






    function geraPix($chave, $idTx = '', $valor = 0.00)
    {

        $this->valor = number_format($valor, 2, '.', '');

        $resultado = "000201";
        $resultado .= $this->formataCampo("26", "0014br.gov.bcb.pix" . $this->formataCampo("01", $chave));
        $resultado .= "52040000"; // Código fixo
        $resultado .= "5303986";  // Moeda (Real)
        if ($valor > 0) {
            $resultado .= $this->formataCampo("54", number_format($valor, 2, '.', ''));
        }
        $resultado .= "5802BR"; // País
        $resultado .= "5901N";  // Nome
        $resultado .= "6001C";  // Cidade
        $resultado .= $this->formataCampo("62", $this->formataCampo("05", $idTx ?: '***'));
        $resultado .= "6304"; // Início do CRC16
        $resultado .= $this->calculaCRC16($resultado); // Adiciona o CRC16 ao final
        return $resultado;
    }













    private function CalculoParcela()
    {
        $qtdParcelas = (float) $this->parcelas;
        $valor = (float) $this->valor;
        $valorParcela = $valor / $qtdParcelas;
    
        // Garantir que o valor da parcela seja pelo menos 5.00
        if ($valorParcela < 5.00) {    $valorParcela = 5.00;  }
    
        $novaQtdParcelas = floor($valor / $valorParcela);
        $valorFinalParcela = $valor / $novaQtdParcelas;
        $r = $valorFinalParcela / floor($novaQtdParcelas); 
    
        /*
        echo "parcela escolhida originalmente:".$this->parcelas;
        echo "<hr>valor: ".$this->valor;
        echo "<hr>Nova quantidade de parcelas: " . $novaQtdParcelas; 
        echo "<hr>Valor final da parcela: " . $valorFinalParcela;
        echo "<hr>Qtd de parcelas original: " . $this->parcelas;
        echo "<hr>Valor do produto: " . $this->valor;
        */

        $this->retornoDadosProcessados['parcelas']=$novaQtdParcelas;
        $this->retornoDadosProcessados['ValorDeCadaParcelas']=round($valorFinalParcela,2);
    }
    



    








































}
