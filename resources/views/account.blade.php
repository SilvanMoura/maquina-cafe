@extends('layouts/app')

@section('content')
<style>
    .col-lg-12,
    .col-lg-3,
    .col-lg-9,
    .col-md-12,
    .col-md-3,
    .col-md-9,
    .col-sm-12,
    .col-xs-12 {
        position: relative;
        min-height: 1px;
    }

    .img-user {
        bottom: 15px;
        right: 18px;
        padding: 6px;
        background: #d4d7df;
        color: #333649;
        border-radius: 50%;
        width: 15px;
        height: 15px;
        align-items: center;
        opacity: 0.8;
        position: absolute;
    }

    .pass-user {
        bottom: 27px;
        right: 40px;
        padding: 6px;
        background: transparent;
        border-radius: 50%;
        width: 15px;
        height: 15px;
        align-items: center;
        opacity: 0.7;
        position: absolute;
    }

    .img-user:before {
        opacity: 1;
    }

    .profileMC {
        margin-top: -60px;
    }

    section .profileMC .profile-img {
        border: 4px solid #e6e9f3;
        padding: 0;
        height: 100px;
        width: 100px;
    }

    @media (min-width: 1200px) {
        .col-lg-3 {
            width: 25%;
        }
    }

    @media (min-width: 1200px) {

        .col-lg-12,
        .col-lg-3,
        .col-lg-9 {
            float: left;
            width: 100%;
        }
    }

    @media (min-width: 480px) and (max-width: 992px) {
        .col-md-3 {
            width: 25%;
        }

        .col-lg-9 {
            width: 85%;
        }
    }

    @media (max-width: 480px) {
        .table-condensed td {
            padding: 4px 5px;
        }

        .table {
            width: 100%;
        }

        .panel-body {
            padding: 0;
        }
    }
    @media (min-width: 1366px) {
        .ajuste-container {
            margin-left: 7% !important;
            max-width: 93% !important;
            max-height: 85vh !important;
            margin-top:-46%
        }
    }
</style>
<div class="ajuste-container" style="height: 90vh; width: 99vw;">
    <div class="span6" style="margin: 1% 1% 0 7%;">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-lock"></i>
                </span>
                <h5>Minha Conta</h5>
            </div>
            <div class="widget-contentMC" style="margin: 1px 0 0;">
                <div class="row-fluid">
                    <div class="span12">
                        <ul class="site-stats">
                            <li class="bg_ls ">
                                <strong>Id: <span id="idUser">{{ $user->id }}</span></strong>
                            </li>
                            <li class="bg_ls">
                                <strong>Nome: {{ $user->name }}</strong>
                            </li>
                            <li class="bg_lg span12" style="margin-left: 0">
                                <strong>Email: {{ $user->email }}</strong>
                            </li>

                            <!-- <li class="bg_lo span12" style="margin-left: 0">
                                <strong>Nível: </strong>
                            </li> -->
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="span6" style="margin-top: 1%">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-lock"></i>
                </span>
                <h5>Alterar Minha Senha</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span12" style="height: 164px">
                        <form id="formSenha">
                            @csrf
                            <div class="span12" style="margin-left: 0">
                                <label for="">Nova Senha</label>
                                <input type="password" id="novaSenha" name="novaSenha" class="span12" />
                            </div>
                            <div class="span12" style="margin-left: 0">
                                <label for="">Confirmar Senha</label>
                                <input type="password" name="confirmarSenha" class="span12" />
                            </div>
                            <button id="btnUpdate" type="submit" class="button btn btn-primary" style="max-width: 140px;text-align: center">
                                <span class="button__icon"><i class='bx bx-lock-alt'></i></span>
                                <span class="button__text2">Alterar Senha</span></button>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>


<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#formSenha').validate({
            rules: {
                novaSenha: {
                    required: true
                },
                confirmarSenha: {
                    equalTo: "#novaSenha"
                }
            },
            messages: {
                novaSenha: {
                    required: 'Campo Requerido.'
                },
                confirmarSenha: {
                    equalTo: 'As senhas não combinam.'
                }
            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        $('#btnUpdate').on('click', function(e) {
            e.preventDefault();

            var id = $("#idUser").text();
            // Validação do formulário usando o plugin validate
            if ($("#formSenha").valid()) {

                var dados = $("#formSenha").serializeArray();
                $.ajax({
                    type: "PUT",
                    url: "https://srv981758.hstgr.cloud/perfil/atualizar/" + id,
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Senha atualizada com sucesso") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Alteração Concluída',
                                text: 'Senha atualizada com sucesso!',
                            }).then(() => {
                                window.location.href = "https://srv981758.hstgr.cloud/dashboard";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro na alteração',
                                text: data.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro na alteração',
                            text: xhr.responseJSON.message,
                        });
                    },
                    complete: function() {
                        // Limpar qualquer indicação visual de loading, se necessário
                    }
                });
            }
        })
    });
</script>
@endsection