# Projeto testeLaravel

## Descrição
Este projeto utiliza o framework Laravel para implementar um sistema de processamento de pagamentos integrado com a API da Asaas. Os arquivos de controle e visualização estão estruturados da seguinte forma:

- **Controller de Pagamento:** `testeLaravel\app\Http\Controllers\payment\pagamentoController.php`
  - Responsável por realizar todas as requisições para a API da Asaas e processar as informações de pagamento.

- **Views:**
  - **Página de Pagamento:** `testeLaravel\resources\views\teste\payment.blade.php`
    - Interface para a seleção do meio de pagamento.
  
  - **Página de Confirmação:** `testeLaravel\resources\views\teste\obrigado.blade.php`
    - Interface exibida após a conclusão do pagamento.

## Funcionalidades
- O usuário acessa a página inicial através de: http://localhost/testeLaravel/public/payment.
- Após selecionar o método de pagamento, é redirecionado para: http://localhost/testeLaravel/public/obrigado.

## Configuração
1. Clone este repositório.
2. Instale as dependências do projeto utilizando o comando: composer install

