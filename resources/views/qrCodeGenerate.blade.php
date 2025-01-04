<!DOCTYPE html>
<html lang="pr-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .form-container {
            margin: 20px auto;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <h1>Gerar QR Code para Máquina</h1>

    <div class="form-container">
        <form method="POST" action="/qrcode">
            @csrf
            <label for="machine_id">Número de Série da Máquina:</label><br>
            <input type="text" id="machine_id" name="machine_id" required><br><br>
            <button type="submit">Gerar QR Code</button>
        </form>
    </div>
</body>
</html>
