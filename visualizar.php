<?php
// Nome da base de dados (pode ser passado como variável ou configurado)
$base_de_dados = $_POST['database']; // Altere conforme necessário

// URL do serviço Go com o parâmetro da base de dados
$api_url = "http://localhost:8080/produtos?db=" . urlencode($base_de_dados);

// Inicializa o cURL
$curl_handle = curl_init();

// Define a URL e outras opções
curl_setopt($curl_handle, CURLOPT_URL, $api_url);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

// Executa a requisição e obtém a resposta
$response = curl_exec($curl_handle);

// Verifica se houve erro na requisição
if (curl_errno($curl_handle)) {
    echo "Erro: " . curl_error($curl_handle);
    curl_close($curl_handle);
    exit;
}

// Fecha a conexão cURL
curl_close($curl_handle);

// Decodifica a resposta JSON
$produtos = json_decode($response, true);

// Verifica se a resposta foi decodificada corretamente
if ($produtos === null) {
    echo "Erro ao decodificar JSON";
    exit;
}

// Encontra o menor preço
$precos = array_column($produtos, 'preco');
$min_preco = min($precos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
        }
        .cabecalho h1 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            color: #333;
        }
        .grid-item {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            transition: box-shadow 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .grid-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .info {
            padding: 20px;
        }
        .title {
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: #333;
        }
        .description {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 20px;
        }
        .price {
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: #333;
        }
        .old-price {
            font-size: 0.875rem;
            color: #999;
            text-decoration: line-through;
        }
        .cabecalho {
            text-align: center;
            margin-bottom: 20px;
        }
        .cardapio {
            max-width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="cabecalho">
    <h1>Seja Bem-vindo, produtos a partir de R$ <?php echo number_format($min_preco, 2, ',', '.'); ?></h1>
</div>

<div class="cardapio">
    <div class="container mt-4">
        <div class="row">
            <?php foreach ($produtos as $produto) { ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="grid-item">
                    <img src="shutterstock_1806472312.jpg" alt="<?php echo htmlspecialchars($produto['descricao']); ?>" class="image">
                    <div class="info">
                        <h2 class="title"><?php echo htmlspecialchars($produto['descricao']); ?></h2>
                        <p class="description"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <?php if ($produto['preco'] > 0) { ?>
                        <p class="old-price">R$ <?php echo number_format($produto['preco'] * (rand(100, 200)/100), 2, ',', '.'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
