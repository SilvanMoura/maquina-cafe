@extends('layouts/app')

@section('content')

<script src="{{ asset('js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/funcoes.js') }}"></script>
<style>

    /* Hiding the checkbox, but allowing it to be focused */
    .badgebox {
        opacity: 0;
    }

    .badgebox+.badge {
        /* Move the check mark away when unchecked */
        text-indent: -999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
    }

    .badgebox:focus+.badge {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */
        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
    }

    .control-group.error .help-inline {
        display: flex;
    }

    .form-horizontal .control-group {
        border-bottom: 1px solid #ffffff;
    }

    .form-horizontal .controls {
        margin-left: 20px;
        padding-bottom: 8px 0;
    }

    .form-horizontal .control-label {
        text-align: left;
        padding-top: 15px;
    }

    .nopadding {
        padding: 0 20px !important;
        margin-right: 20px;
    }

    .widget-title h5 {
        padding-bottom: 30px;
        text-align-last: left;
        font-size: 2em;
        font-weight: 500;
    }

    .control-label {
        margin-right: -30px; 
    }

    @media (max-width: 480px) {
        form {
            display: contents !important;
        }

        .form-horizontal .control-label {
            margin-bottom: -6px;
        }

        .btn-xs {
            position: initial !important;
        }
    }
    @media (min-width: 1366px) {
        .ajuste-container {
            margin-left: 7% !important;
            max-width: 85% !important;
            max-height: 85vh !important;
            margin-top:-46%
        }
    }
    @media (max-width: 1365px) {
        .ajuste-container {
            width: 91vw;
        }
    }
</style>
<div class="ajuste-container" style="height: 92vh;">
    <div class="row-fluid" style="margin: 0% 0% 0 7%; ">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <h5>Cadastrar Novo Módulo</h5>
                </div>

                <div class="alert alert-danger hide" id="error-message"></div>

                <form id="formModule" class="form-horizontal">
                    @csrf
                    <div class="widget-content nopadding tab-content" style="display: flex; justify-content: center; align-items:center; margin: auto;">
                        <div class="span12" style="max-width: 400px; width: 100%;">

                            <div class="control-group">
                                <label for="module" class="control-label">Código do módulo - MCCF:</label>
                                <div class="controls">
                                    <input id="module" type="number" name="module" value="" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex; justify-content: center">
                                <button id="btnRegister" type="submit" class="button btn btn-mini btn-success">
                                    <span class="button__icon"><i class='bx bx-save'></i></span>
                                    <span class="button__text2">Salvar</span>
                                </button>
                                <a title="Voltar" class="button btn btn-warning" href="/clientes">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span>
                                    <span class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $("#module").focus();
        $('#formModule').validate({
            rules: {
                module: {
                    required: true
                }
            },
            messages: {
                module: {
                    required: 'Campo Requerido.'
                },
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

        $('#btnRegister').on('click', function(e) {
            e.preventDefault();

            // Validação do formulário usando o plugin validate
            if ($("#formModule").valid()) {
                var dados = $("#formModule").serialize();

                $(this).addClass('disabled');
                $('#progress-acessar').removeClass('hide');

                // Requisição AJAX
                $.ajax({
                    type: "POST",
                    url: "https://8a7444e0a6fd.ngrok-free.app/modulos/adicionar",
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Módulo criado com sucesso") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cadastro Concluído',
                                text: 'Módulo criado com sucesso!',
                            }).then(() => {
                                window.location.href = "https://8a7444e0a6fd.ngrok-free.app/";
                            });
                        } else {
                            $('#error-message').text(data.message || 'Erro no cadastro. Por favor, tente novamente.');
                            $('#error-message').removeClass('hide');
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
        })
    });
</script>

@endsection