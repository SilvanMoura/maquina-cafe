<!DOCTYPE html>
<html lang="pt-br">
<?php header('Access-Control-Allow-Origin: *'); ?>

<head>
    <title>{{ config('app_name') }} </title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap-responsive.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/matrix-login.css') }}" />
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/cafe-fazenda-logo-sistema.png') }}" />
    <script src="{{ asset('js/jquery-1.12.4.min.js') }}"></script>
</head>

<body>
    <div class="main-login">
        <div class="left-login">
            <!-- Saudação -->
            <h1 class="h-one">
                <?php
                function saudacao($nome = '')
                {
                    $hora = date('H');
                    if ($hora >= 8 && $hora < 12) {
                        return 'Olá! Bom dia' . (empty($nome) ? '' : ', ' . $nome);
                    } elseif ($hora >= 12 && $hora < 18) {
                        return 'Olá! Boa tarde' . (empty($nome) ? '' : ', ' . $nome);
                    } else {
                        return 'Olá! Boa noite' . (empty($nome) ? '' : ', ' . $nome);
                    }
                }
                $login = 'bem-vindo';
                echo saudacao($login);

                ?>
            </h1>

            <h2 class="h-two"> Ao Sistema do Módulo de Controle</h2>
            <img src="{{ asset('images/dashboard-animate.svg') }}" class="left-login-image" alt="Map-OS - Versão: {{ config('app_version') }}">
        </div>



        <div id="loginbox">
            <form class="form-vertical" id="formLogin">
                @csrf

                @if (session('error'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
                @endif

                <div class="d-flex flex-column">
                    <div class="right-login">
                        <div class="container">
                            <div class="card">
                                <div class="content">
                                    <div id="newlog">
                                        <div>
                                            <img style="width: 100px; height: 100px;" src="{{ asset('images/cafe-fazenda-logo-sistema.png') }}" alt="Logo do café da fazenda">
                                        </div>
                                    </div>
                                    <div id="mcell">Versão: 1.0</div>

                                    <div class="input-field">
                                        <label class="fas fa-user" for="nome"></label>
                                        <input id="email" name="email" type="text" placeholder="Email">
                                    </div>
                                    <div class="input-field">
                                        <label class="fas fa-lock" for="password"></label>
                                        <input name="password" type="password" placeholder="Senha">
                                    </div>
                                    <div class="center"><button id="btn-acessar">Acessar</button></div>
                                    <div class="links-uteis">
                                        <a href="https://github.com/SilvanMoura">
                                            <p>{{ date('Y') }} &copy; Silvan Moura</p>
                                        </a>
                                    </div>
                                    <a href="#notification" id="call-modal" role="button" class="btn" data-toggle="modal" style="display: none">notification</a>
                                    <div id="notification" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-header">
                                            <h4 id="myModalLabel">Mensagem</h4>
                                        </div>
                                        <div class="modal-body">
                                            <h5 style="text-align: center" id="message">Os dados de acesso estão incorretos, por favor tente novamente!</h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="#notification" id="call-modal" role="button" class="btn" data-toggle="modal" style="display: none">notification</a>
                <div id="notification" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <h4 id="myModalLabel">Mensagem</h4>
                    </div>
                    <div class="modal-body">
                        <h5 style="text-align: center" id="message">Os dados de acesso estão incorretos, por favor tente novamente!</h5>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Fechar</button>
                    </div>
                </div>

                <script src="{{ asset('js/bootstrap.min.js') }}"></script>
                <script src="{{ asset('js/validate.js') }}"></script>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#email').focus();

                        $('#btn-acessar').on('click', function(e) {
                            e.preventDefault();

                            // Validação do formulário usando o plugin validate
                            if ($("#formLogin").valid()) {
                                var dados = $("#formLogin").serialize();

                                $(this).addClass('disabled');
                                $('#progress-acessar').removeClass('hide');
                                
                                $.ajax({
                                    type: "POST",
                                    url: "https://srv981758.hstgr.cloud/login",
                                    data: dados,
                                    dataType: 'json',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(data) {
                                        if (data.success === true) {
                                            window.location.href = "https://srv981758.hstgr.cloud/dashboard";
                                        } else {
                                            $('#btn-acessar').removeClass('disabled');
                                            $('#progress-acessar').addClass('hide');

                                            $('#message').text(data.message || 'Os dados de acesso estão incorretos, por favor tente novamente!');
                                            $('#call-modal').trigger('click');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Erro na requisição AJAX:", error);
                                        // Adicione manipulação de erro conforme necessário
                                    },
                                    complete: function() {
                                        // Limpar qualquer indicação visual de loading, se necessário
                                    }
                                });
                            }
                        });

                        // ... (Seu código de validação existente aqui, remova submitHandler)
                    });
                </script>
            </form>
        </div>
    </div>
</body>

</html>