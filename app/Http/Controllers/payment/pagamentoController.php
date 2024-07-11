<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;








class pagamentoController extends Controller
{

    private $minhaChave = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwODQyNTk6OiRhYWNoXzg1MTkyNmI3LWUzZGQtNDk1Zi05MDEwLTEzYjBiNWIwY2IwMg==';
    //private $minhaChave =  '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwODQyNTk6OiRhYWNoXzg1MTkyNmI3LWUzZGQtNDk1Zi05MDEwLTEzYjBiNWIwY2IwMg==';
    private $cliente = "cus_000006095618";
    //private $url="https://www.asaas.com/api/v3/payments/"; 
    private $url="https://sandbox.asaas.com/api/v3/payments/";
    private $response;


    public function index()
    {
        return view('teste.payment');
    }



    /*************************************************/
    /*************************************************/
    private function TrataValor($x)
    {
        $valor = str_replace("R$", "", $x);
        $valor2 = str_replace(".", "", $valor);
        $valor3 = str_replace(",", ".", $valor2);
        return $valor3;
    }
    /*************************************************/
    /*************************************************/





    /***Aqui eu vou acesaar a api do Asaas***/
    public function Api(Request $request)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');




        // Verifica se os dados foram recebidos corretamente
        $data = $request->all();
        $data['dueDate'] = date('Y-m-d', strtotime('+3 days'));
        $data['customer'] = $this->cliente;
        $data['value'] = $this->TrataValor($data['value']);
        $data['description'] = "api de pagamento";

        $this->TrataValor($data['value']);


        // Define manualmente os parâmetros obrigatórios do pagamento
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
                echo json_encode(['error' => 'Parâmetro obrigatório ausente: ' . $param]);
                return;
            }
        }

        $dataToSend = array_merge($dataToSend, $data);

        $url = $this->url;// 'https://sandbox.asaas.com/api/v3/payments';
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
            echo json_encode(['error' => 'Erro ao processar a requisição: ' . $response]);
        } else {


            $this->response = json_decode($response, true);

            /*echo json_encode([
                'url' => $url,
                'response' => json_decode($response, true),
                'http_code' => $httpCode
            ]);*/
        }
        // Fecha a conexão cURL
        curl_close($ch);

        $funcao = $data['billingType'];
        $this->$funcao();
    }









    public function PIX()
    {
        $idPix = $this->response['id'];

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

        if ($httpCode >= 400) {
            echo 'Erro ao obter o QR Code: ' . $response;
        } else {
            $responseData = json_decode($response, true);
            /*********************************************/
            /*********************************************/
            /*********************************************/
            if (is_string($response)) {
                $data = json_decode($response, true);
            } else {
                // Se já for um objeto ou array, não precisa decodificar
                $data = $response;
            }
            if (is_array($data)) {
               echo json_encode(array("tipo"=>"pix","codigoPix"=>$this->criaCodigoPix(),"qrCode"=>$data['encodedImage']));
            } else {
                echo 'Response não é um JSON válido';
            }
            /*********************************************/
            /*********************************************/
            /*********************************************/
        }

        curl_close($ch);
    }













    
    public function criaCodigoPix()
    {
        // Dados do pagamento recebido da Asaas
        $valor = $this->response['value']; // Valor do pagamento
        $descricao = $this->response['description']; // Descrição do pagamento
    
        // Chave PIX (substitua pela chave PIX válida da sua conta)
        $chavePix = '28630597852';
    
        // Preparar o payload PIX
        $payload = Payload::mech(
            merchantName: 'Nome do beneficiário',
            merchantCity: 'Cidade',
            amount: $valor,
            txid: 'IdentificadorUnico',
            description: $descricao,
            pixKey: $chavePix
        );
    
        // Gerar o código PIX em formato base64
        $base64Payload = base64_encode($payload);
    
        // Montar o código PIX completo
        $pixCode = '00020126360014BR.GOV.BCB.PIX0112' . strlen($base64Payload) . $base64Payload . '6304';
    
        // Retornar o código PIX completo
        return $pixCode;
    }






















}
