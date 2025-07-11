<div class="qr-container">
    <h2 class="qr-title">Escaneie o QR Code da máquina</h2>
    <h4 class="qr-title">Se tiver dificuldades, recarregue a página</h4>

    <div id="reader" class="qr-reader"></div>

    <div id="resultado" class="qr-result"></div>
</div>

{{-- Estilos --}}
<style>
    .qr-container {
        text-align: center;
        padding: 50px;
        max-width: 100vw;
        overflow-x: hidden;
    }

    .qr-title {
        margin-bottom: 20px;
    }

    .qr-reader {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        padding: 10px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        background-color: #000;
    }

    #reader video {
        width: 100% !important;
        height: auto !important;
        object-fit: cover;
        border-radius: 10px;
        display: block;
    }

    .qr-result {
        margin-top: 20px;
        font-size: 16px;
        color: #333;
        word-wrap: break-word;
    }

    .qr-success {
        color: green;
    }

    .qr-error {
        color: red;
    }
</style>

{{-- Script --}}
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // Obtenha o ID do cupom passado pela URL
    const cupomId = "{{ $cupomId }}"; // Recebe o valor do ID do cupom da URL

    function extrairReferenceId(emvString) {
        const match = emvString.match(/https:\/\/mpago\.la\/pos\/(\d{9})\d*/);
        return match ? match[1] : null;
    }

    const html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start({
            facingMode: "environment"
        }, {
            fps: 10,
            qrbox: 250
        },
        (decodedText, decodedResult) => {
            html5QrCode.stop();

            const referenceId = extrairReferenceId(decodedText);
            if (referenceId) {
                document.getElementById("resultado").innerHTML = `
                    <p><strong>POS ID:</strong> ${referenceId}</p>
                    <p>Enviando cupom para o dispositivo...</p>
                `;

                fetch("https://74d3-2804-14d-403a-8011-4019-f63b-2c27-f3fc.ngrok-free.app/readCode", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            pos_id: referenceId,
                            cupom_id: cupomId // Passando o ID do cupom para o backend
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("resultado").innerHTML += `
                            <p class="qr-success"><strong>${data.status}</strong></p>
                        `;
                    })
                    .catch(() => {
                        document.getElementById("resultado").innerHTML += `
                            <p class="qr-error">Erro ao enviar cupom.</p>
                        `;
                    });

            } else {
                document.getElementById("resultado").innerHTML = `
                    <p class="qr-error">QR Code inválido.</p>
                `;
            }

            document.getElementById("reader").style.display = "none"; // Esconde a câmera após a leitura
        },
        (errorMessage) => {
            // Leitura falhou (ignorado)
        }
    ).catch((err) => {
        console.error("Erro ao iniciar câmera:", err);
        document.getElementById("resultado").innerHTML = `
            <p class="qr-error">Erro ao acessar a câmera.</p>
        `;
    });

    // Quando a página for recarregada ou ao clicar em "Cancelar", reexibe o leitor
    function resetLeitor() {
        document.getElementById("reader").style.display = "block";
        document.getElementById("resultado").innerHTML = ""; // Limpa o resultado
    }
</script>