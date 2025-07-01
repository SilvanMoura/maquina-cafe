@extends('layouts/app')

@section('content')

{{-- Estilos --}}
<style>
    .qr-wrapper {
        padding: 20px;
        max-width: 82vw;
        box-sizing: border-box;
        text-align: center;
        font-family: Arial, sans-serif;
        margin-left: 5%;
    }

    .qr-title {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }

    .qr-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px;

    }

    .qr-form label {
        font-weight: bold;
        color: #555;
    }

    .qr-form select {

        font-size: 16px;
        width: 200px;
    }

    /* Contêiner para os botões */
    .botao-container {
        display: grid;
        grid-template-columns: repeat(1, 0.3fr);
        grid-gap: 10px;
        max-width: 600px;
        margin: 20px auto;
        align-items: center;
        justify-content: center;
    }

    /* Estilo de cada botão */
    .botao {
        padding: 15px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .botao:hover {
        background-color: #45a049;
    }

    .qr-camera {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-radius: 8px;
        overflow: hidden;
        background: #000;
    }

    .qr-camera video {
        width: 100% !important;
        height: auto !important;
        object-fit: cover;
    }

    .control-output {
        margin-top: 20px;
        font-size: 16px;
        color: #222;
    }

    .qr-success {
        color: green;
    }

    .qr-error {
        color: red;
    }

    @media (min-width: 1366px) {
        .ajuste {
            max-height: 85vh !important;
            margin-top: -45%;

        }

        .ajuste-container {
            margin-left: 13% !important;
            max-width: 85% !important;

        }
    }

    @media (max-width: 1365px) {
        .ajuste-container {
            width: 91vw;
        }
    }
</style>

<div class="qr-wrapper ajuste" style="height: 92vh;">
    <div class="ajuste-container">
        <h1 class="qr-title">Controle Remoto</h1>

        <div class="qr-form">
            <div class="control-group">
                <label for="modulo" class="control-label">Modulo MCCF</label>
                <div class="controls">
                    <select id="modulo" name="modulo" class="form-control">
                        @foreach($modulesData as $f)
                        <option value="{{ $f->modulo }}">{{ $f->modulo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="botao-container">
            <button class="botao" data-id="1">1</button>
            <button class="botao" data-id="2">2</button>
            <button class="botao" data-id="3">3</button>
            <button class="botao" data-id="4">4</button>
        </div>

        <button class="botao" data-id="Programação">Programação</button>

        <div id="resultado" class="control-output"></div>
    </div>
</div>

{{-- Script --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Seleciona todos os botões com a classe "botao"
        const botoes = document.querySelectorAll(".botao");

        // Adiciona o listener de clique para cada botão
        botoes.forEach(botao => {
            botao.addEventListener("click", function() {
                const valorBotao = this.getAttribute("data-id");
                const moduloSelecionado = document.getElementById("modulo").value;

                // Exibe os valores no console
                console.log("Botão clicado:", valorBotao);
                console.log("Módulo selecionado:", moduloSelecionado);

                // Exemplo: pode exibir na div de resultado
                document.getElementById("resultado").innerHTML = `
                    <p><strong>Botão:</strong> ${valorBotao}</p>
                    <p><strong>Módulo:</strong> ${moduloSelecionado}</p>
                    <p id="resultado-status"><strong>Status:</strong> Enviando </p>
                `;

                fetch("http://127.0.0.1:8000/sendCommand", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            modulo: moduloSelecionado,
                            botao: valorBotao
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("resultado").innerHTML = `
                        <p>${data.status || 'Sucesso!'}</p>
                    `;
                    })
                    .catch(() => {
                        document.getElementById("resultado").innerHTML = `
                        <p>Erro ao enviar os dados.</p>
                    `;
                    });
            });
        });
    });



    /* html5QrCode.start({
            facingMode: "environment"
        }, {
            fps: 10,
            qrbox: 250
        },
        (decodedText) => {
            html5QrCode.stop();

            if (!selectedModulo || !selectedBotao) {
                resultadoDiv.innerHTML = `<p class="qr-error">Selecione um módulo e um botão.</p>`;
                return;
            }

            const referenceId = extrairReferenceId(decodedText);
            if (!referenceId) {
                resultadoDiv.innerHTML = `<p class="qr-error">QR Code inválido.</p>`;
                return;
            }

            resultadoDiv.innerHTML = `<p>Enviando dados...</p>`;

            fetch("https://74d3-2804-xxxx.ngrok-free.app/readCode", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        pos_id: referenceId,
                        cupom_id: cupomId,
                        modulo: selectedModulo,
                        botao: selectedBotao
                    })
                })
                .then(res => res.json())
                .then(data => {
                    resultadoDiv.innerHTML = `<p class="qr-success">${data.status || 'Sucesso!'}</p>`;
                })
                .catch(() => {
                    resultadoDiv.innerHTML = `<p class="qr-error">Erro ao enviar os dados.</p>`;
                });

            readerDiv.style.display = "none";
        },
        (err) => {
            // Ignorado
        }
    ).catch(err => {
        resultadoDiv.innerHTML = `<p class="qr-error">Erro ao iniciar câmera.</p>`;
    }); */
</script>
@endsection