# Outros Processos

Documentação de processos administrativos, financeiros e de cadastro de produtos.

## Sistemas Administrativos

- **ERP Financeiro:** [erp.velzani.com/wp-admin](https://erp.velzani.com/wp-admin) — Contas a pagar, notas fiscais e controle financeiro.
- **Clara:** Cartão corporativo virtual utilizado para pagamentos da empresa.
- **Klaviyo:** Plataforma de e-mail marketing e automações.
- **Amazon Seller Central:** Gestão da loja na Amazon (códigos de verificação enviados por e-mail).

### Diferença entre os Painéis

- Para **produtos e remessas**, use sempre o Prod-Ops.
- Para **financeiro e contas a pagar**, use o ERP.
- O **site principal** (elevacalcados.com.br) é onde os pedidos são gerenciados no WooCommerce.

## Contas a Pagar

### Cadastro de Despesas no ERP

Ao registrar uma despesa no ERP:

1. Escolha a **classificação correta**:
   - TroqueCommerce → "Logística"
   - Bling → verificar categoria apropriada
   - Outros serviços → de acordo com a natureza

2. Anexe a **Nota Fiscal** (NF) sempre que possível.

3. Se disponível, prefira enviar o arquivo **XML** ao invés de PDF — o XML preenche os dados automaticamente.

4. Para valores monetários, use a vírgula/ponto **apenas no separador decimal** (ex.: `1480,00` e não `1.480,00`). Valores com separador de milhares podem ser registrados incorretamente.

5. Inclua o link do boleto/Asaas nas observações quando aplicável (PIX pode expirar, então o link do Asaas é preferível).

### Cartão Clara

O cartão corporativo Clara pode ser utilizado para pagamentos operacionais. Regras:

- Sempre que fizer um pagamento pelo Clara, faça o upload da documentação de suporte (NF ou recibo) diretamente no sistema da Clara.
- Para pagamentos nacionais: enviar NF.
- Para pagamentos internacionais: enviar recibo.
- Caso precise de aumento de limite, solicite aprovação.

### Notas Fiscais de Prestadores

Prestadores de serviço devem enviar suas notas fiscais, preferencialmente em formato XML. Se o prestador não conseguir emitir NF temporariamente, alinhe uma solução provisória.

## Cadastro de Produtos

### Painel de Produtos

O cadastro de produtos é feito no Prod-Ops. Ferramentas úteis:

- **Kanban de Lançamentos:** [prod-ops.velzani.com/wp-admin/admin.php?page=velzani-prod-launching-kanban](https://prod-ops.velzani.com/wp-admin/admin.php?page=velzani-prod-launching-kanban)
- **Verificador de Imagens:** [prod-ops.velzani.com/wp-admin/admin.php?page=velzani-image-checker](https://prod-ops.velzani.com/wp-admin/admin.php?page=velzani-image-checker) — Identifica imagens que não estão no formato correto.
- **Produtos Estruturados:** [prod-ops.velzani.com/wp-admin/admin.php?page=velzani-structured-products](https://prod-ops.velzani.com/wp-admin/admin.php?page=velzani-structured-products) — Visão geral de todos os produtos com estoque.

### Status dos Produtos

- **Ativo:** Produto visível no site e pronto para venda.
- **Em desenvolvimento:** Produto em fase de criação/revisão (não visível no site).
- **Aguardando lançamento:** Produto com dados completos, aguardando data de lançamento.

**Importante:** Só coloque um produto como "Ativo" quando TODAS as informações estiverem preenchidas (fotos, preço, custo, descrição, NCM, etc.), pois isso aciona a sincronização com o site.

### Fotos dos Produtos

- As fotos devem ser em **formato quadrado** (1:1) para exibição correta no site.
- Usar **alta resolução** sempre que possível.
- Ao subir fotos pelo WhatsApp, o formato é convertido para JPEG. Para banners e imagens do site, baixe diretamente o arquivo original (preferencialmente WebP).
- Não utilizar a "foto de identificação" no cadastro, pois pode causar bugs de duplicação.
- No Drive, salve sempre a versão em **alta definição** (original).

### Edição Simultânea

Se outra pessoa estiver editando o mesmo produto, o WordPress exibirá um aviso. Nesse caso, alinhe com o colega para "tomar a edição" ou aguarde ele terminar.

## Caixas de Embalagem

As caixas personalizadas são encomendadas do fornecedor Flávio (São Paulo). O processo de pedido:

1. Fazer o pedido das caixas com o Flávio.
2. Agendar coleta com a transportadora.
3. A transportadora coleta as caixas e entrega no CD em Franca (geralmente em 2-3 dias úteis).

## Fornecedores e Fábricas

- **Rafarillo:** Parceiro de produção de calçados.
- **Ettstec:** Rua Itainópolis, 239 — Bairro Cidade Aracília, CEP 07250-170.
- **Pravini, Naves (BSC):** Outros fornecedores de calçados. Para identificar correspondência entre códigos da NF e modelos internos, consultar o Laionel ou as notas fiscais.

## Endereços Importantes

- **CD Velzani (Franca/SP):** Avenida São Vicente, 7718 — CEP: 14.412-348, Franca/SP.
- **Cubbo (Fulfillment):** Estrada Maria Imaculada, 31, Módulo 5A, Jardim Santa Clara, Embu das Artes — SP, 06843-010.

## Links Úteis

- **Configurações do Tema:** [elevacalcados.com.br/wp-admin/admin.php?page=velzani-theme-settings](https://elevacalcados.com.br/wp-admin/admin.php?page=velzani-theme-settings)
