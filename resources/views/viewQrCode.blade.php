<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Link</title>
    <style>
        #qrCodeContainer img {
            width: 200px; /* Largura desejada */
            height: 200px; /* Altura desejada */
        }

        #printArea {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>QR Code</h1>
    
    <!-- Exibe o link de QR code -->
    <div id="qrCodeContainer"></div>
    <div id="printArea">
        <button onclick="printQrCode()">Imprimir QR Code</button>
    </div>

    <script>
        // Aqui você vai acessar a variável 'qrCodeLink' que foi passada para a view
        var qrCodeLink = @json($qrCodeLink);
        
        // A URL do QR code
        var qrCodeUrl = qrCodeLink.original[0]['href'];
        console.log(qrCodeUrl);

    // Mostrar o QR code como imagem dentro do container
        var imgTag = document.createElement('img'); // Cria a tag <img>
        imgTag.src = qrCodeUrl; // Define o atributo src como o link do QR code
        imgTag.alt = "QR Code"; // Atributo alt para acessibilidade

        // Adiciona a tag <img> no container
        document.getElementById('qrCodeContainer').appendChild(imgTag);

        function printQrCode() {
            var printWindow = window.open('', '_blank'); // Abre uma nova janela ou aba
            printWindow.document.write('<html><head><title>Imprimir QR Code</title></head><body>');
            printWindow.document.write('<h1>QR Code</h1>');
            printWindow.document.write('<img src="' + qrCodeUrl + '" alt="QR Code" style="display: block; width: 150px; height: 150px;">');
            printWindow.document.write('</body></html>');
            printWindow.document.close(); // Fecha o documento
            printWindow.print(); // Abre a caixa de diálogo de impressão
        }
</script>

</body>
</html>
