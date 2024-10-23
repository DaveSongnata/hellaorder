<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Base de Dados</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Selecione a Base de Dados</h2>
    <?php
    if (isset($_GET['error'])) {
        $error_message = htmlspecialchars($_GET['error']);
        echo "<div class='alert alert-danger' role='alert'>
                <strong>Erro!</strong> $error_message
              </div>";
    }
    ?>
    <form action="visualizar.php" method="post">
        <div class="form-group">
            <label for="database">Nome da Base de Dados:</label>
            <input type="text" class="form-control" id="database" name="database" placeholder="Digite o nome da base de dados" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>
</body>
</html>
