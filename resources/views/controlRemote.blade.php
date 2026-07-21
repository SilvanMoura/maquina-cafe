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

    .control-label {
        margin-right: 10px;
    }

    .valor-input {
        display: flex;
        align-items: center;
    }

    .valor-input input {
        width: 80px;
        font-size: 16px;
    }

    .valor-input span {
        margin-left: 2px;
        font-size: 16px;
        /* Mesmo tamanho do input */
        color: #666;
        /* Um pouco mais discreto */
        font-weight: 500;
        line-height: 1;
        user-select: none;
    }

    .credito-card {
        display: flex;
        align-items: end;
        gap: 24px;
        padding: 20px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        flex-wrap: wrap;
    }

    .campo {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 220px;
    }

    .campo-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .campo-input {
        display: flex;
        align-items: center;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 0 12px;
        height: 44px;
        transition: border-color .2s, box-shadow .2s;
    }

    .campo-input:focus-within {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }

    .campo-input select,
    .campo-input input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        width: 100%;
        color: #111827;
    }

    .campo-input.valor input {
        width: 80px;
    }

    .campo-input.valor span {
        margin-left: 4px;
        font-size: 15px;
        font-weight: 600;
        color: #6b7280;
        user-select: none;
    }




    .qr-title {
        text-align: center;
        margin-bottom: 25px;
        font-size: 30px;
        font-weight: 600;
        color: #333;
    }

    .config-card,
    .controle-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 22px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .form-field {
        display: flex;
        flex-direction: column;
    }

    .form-field label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
    }

    .form-field select {
        width: 100%;
        max-width: 320px;
        height: 42px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 15px;
        transition: .2s;
    }

    .form-field select:focus {
        border-color: #4f8dfd;
        box-shadow: 0 0 0 3px rgba(79, 141, 253, .15);
        outline: none;
    }

    .botao-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
        gap: 15px;
    }

    .botao {
        height: 55px;
        border: none;
        border-radius: 10px;
        background: #2d7ef7;
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }

    .botao:hover {
        background: #1f6fe8;
        transform: translateY(-2px);
    }

    .botao:active {
        transform: scale(.97);
    }

    .control-output {
        margin-top: 20px;
        padding: 15px;
        min-height: 50px;
        border-radius: 8px;
        background: #f8f9fa;
        border: 1px solid #e5e7eb;
        font-size: 15px;
        color: #444;
    }

    .tabs-container {
        margin: 20px;
    }

    .custom-tabs {
        display: flex;
        gap: 12px;
        border: none;
        padding: 0;
        margin: 0;
    }

    .custom-tabs>li {
        margin: 0;
    }

    .custom-tabs>li>a {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 12px 22px;
        border: none !important;
        border-radius: 10px;
        background: #f4f6f9;
        color: #555;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;

        transition: .25s;
    }

    .custom-tabs>li>a:hover {
        background: #e9eef8;
        color: #2d7ef7;
    }

    .custom-tabs>li.active>a,
    .custom-tabs>li.active>a:hover,
    .custom-tabs>li.active>a:focus {
        background: #2d7ef7;
        color: #fff;
        border: none !important;
        box-shadow: 0 4px 12px rgba(45, 126, 247, .25);
    }

    .custom-tabs i {
        font-size: 16px;
    }

    .acoes {
        display: flex;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn-enviar-credito {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        min-width: 220px;
        height: 48px;

        border: none;
        border-radius: 8px;

        background: #2d7ef7;
        color: #fff;

        font-size: 15px;
        font-weight: 600;

        cursor: pointer;
        transition: .2s;
    }

    .btn-enviar-credito:hover {
        background: #1f6fe8;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(45, 126, 247, .25);
    }

    .btn-enviar-credito:active {
        transform: scale(.98);
    }

    .btn-enviar-credito i {
        font-size: 16px;
    }

    @media (max-width: 640px) {
        .credito-card {
            flex-direction: column;
            align-items: stretch;
        }

        .campo {
            width: 100%;
            min-width: auto;
        }
    }

    @media (min-width: 1366px) {
        .ajuste {
            max-height: 85vh !important;
            margin-top: -50%;
        }

        .ajuste-container {
            margin-left: 10% !important;
            max-width: 93% !important;
            max-height: 85vh !important;
            margin-top: -46%
        }
    }

    @media (max-width: 1365px) {
        .ajuste-container {
            width: 91vw;
        }
    }
</style>

<div class="ajuste-container" style="height: 90vh; width: 991w;">
    <!-- <div class="new122" style="margin: 1% 1% 0% 1%;">
        <ul class="nav nav-tabs">
            <li><a data-toggle="tab" href="#tab1">Comandos</a></li>
            <li><a data-toggle="tab" href="#tab2">Creditos</a></li>
        </ul>
    </div> -->
    <div class="tabs-container">
        <ul class="nav nav-tabs custom-tabs">
            <li class="active">
                <a data-toggle="tab" href="#tab1">
                    <i class="fa fa-gamepad"></i>
                    Comandos
                </a>
            </li>

            <li>
                <a data-toggle="tab" href="#tab2">
                    <i class="fa fa-credit-card"></i>
                    Créditos
                </a>
            </li>
        </ul>
    </div>
    <div class="widget-content tab-content new122" style="margin: 1% 1% 0% 1%;">
        <!-- <div id="tab1" class="tab-pane active" style="max-height: auto">
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
                <button class="botao" data-id="5">5</button>
            </div>

            <div id="resultado" class="control-output"></div>
        </div> -->
        <div id="tab1" class="tab-pane active">

            <h1 class="qr-title">Controle Remoto</h1>

            <div class="config-card">

                <div class="form-field">
                    <label for="moduloComand">Módulo MCCF</label>

                    <select id="moduloComand" name="moduloComand" class="form-control">
                        @foreach($modulesData as $f)
                        <option value="{{ $f->modulo }}">{{ $f->modulo }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="controle-card">

                <div class="botao-container">
                    <button class="botao" data-id="1">1</button>
                    <button class="botao" data-id="2">2</button>
                    <button class="botao" data-id="3">3</button>
                    <button class="botao" data-id="4">4</button>
                    <button class="botao" data-id="5">5</button>
                </div>

                <div id="resultado" class="control-output"></div>

            </div>

        </div>


        <!--Tab 2-->
        <div id="tab2" class="tab-pane">

            <h1 class="qr-title">Enviar Créditos</h1>

            <div class="credito-card">

                <div class="campo">
                    <label for="moduloCredit" class="campo-label">Módulo MCCF</label>

                    <div class="campo-input">
                        <select id="moduloCredit" name="moduloCredit">
                            @foreach($modulesData as $f)
                            <option value="{{ $f->modulo }}">{{ $f->modulo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label for="pulsos" class="campo-label">Valor do Crédito</label>

                    <div class="campo-input valor">
                        <input type="number" id="pulsos" placeholder="5" min="0" step="1">
                        <span>.00</span>
                    </div>
                </div>

                <div class="acoes">
                    <button id="btnEnviarCredito" class="btn-enviar-credito">
                        <i class="fa fa-paper-plane"></i>
                        Enviar Créditos
                    </button>
                </div>

            </div>

        </div>

    </div>
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
                const moduloSelecionado = document.getElementById("moduloComand").value;

                // Exibe os valores no console
                console.log("Botão clicado:", valorBotao);
                console.log("Módulo selecionado:", moduloSelecionado);

                // Exemplo: pode exibir na div de resultado
                document.getElementById("resultado").innerHTML = `
                    <p><strong>Botão:</strong> ${valorBotao}</p>
                    <p><strong>Módulo:</strong> ${moduloSelecionado}</p>
                    <p id="resultado-status"><strong>Status:</strong> Enviando </p>
                `;

                fetch("https://srv981758.hstgr.cloud/sendCommand", {
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
    $('.custom-tabs a').on('click', function() {
        $('.custom-tabs li').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#btnEnviarCredito').on('click', enviarCreditos);

    function enviarCreditos() {

        const modulo = $('#moduloCredit').val();
        const creditos = $('#pulsos').val();

        if (!creditos || Number(creditos) <= 0) {
            alert('Informe um valor de crédito válido.');
            return;
        }

        $('#btnEnviarCredito')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Enviando...');

        $.ajax({
            url: '/creditos/enviar', // altere para sua rota
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                modulo: modulo,
                creditos: creditos
            },
            success: function(response) {

                $('#btnEnviarCredito')
                    .prop('disabled', false)
                    .html('<i class="fa fa-paper-plane"></i> Enviar Créditos');

                alert(response.message || 'Créditos enviados com sucesso!');
            },
            error: function(xhr) {

                $('#btnEnviarCredito')
                    .prop('disabled', false)
                    .html('<i class="fa fa-paper-plane"></i> Enviar Créditos');

                let mensagem = 'Erro ao enviar créditos.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensagem = xhr.responseJSON.message;
                }

                alert(mensagem);
            }
        });
    }


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