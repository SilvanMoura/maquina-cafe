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
        .ajuste {
            max-height: 85vh !important;
            margin-top: -50%;
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
<div class="ajuste" style="height: 92vh;">
    <div class="row-fluid ajuste-container" style="margin: 0% 0% 0 7%; width: 91vw;">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <h5>Cadastrar Novo Cupom</h5>
                </div>

                <div class="alert alert-danger hide" id="error-message"></div>

                <form id="formCoupon" class="form-horizontal">
                    @csrf
                    <div class="widget-content nopadding tab-content">
                        <div class="span4">

                            <div class="control-group">
                                <label for="name" class="control-label required" style="width:auto; margin-right: 25px;">Nome</label>
                                <div class="controls">
                                    <input id="name" type="text" name="name" value="" />
                                </div>
                            </div>

                        </div>

                        <div class="span4">

                            <div class="control-group">
                                <label for="value" class="control-label required" style="width:auto; margin-right: 25px;">Valor</label>
                                <div class="controls">
                                    <input id="value" type="number" name="value" min="1" value="" />
                                </div>
                            </div>

                        </div>

                        <div class="span4">

                            <div class="control-group">
                                <label for="telefone" class="control-label required" style="width:auto; margin-right: 25px;">Telefone Destino</label>
                                <div class="controls">
                                    <input id="telefone" type="text" name="telefone" value="" />
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button id="btnRegister" type="submit" class="button btn btn-mini btn-success"><span class="button__icon"><i class='bx bx-save'></i></span> <span class="button__text2">Salvar</span></a></button>
                                <a title="Voltar" class="button btn btn-warning" href="/clientes"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
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

        $("#name").focus();

        $('#btnRegister').on('click', function(e) {
            e.preventDefault();

            // Validação do formulário usando o plugin validate
            if ($("#formCoupon").valid()) {
                var dados = $("#formCoupon").serialize();

                $(this).addClass('disabled');
                $('#progress-acessar').removeClass('hide');

                // Requisição AJAX
                $.ajax({
                    type: "POST",
                    url: "http://127.0.0.1:8000/cupons/adicionar",
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Cupom Criado com sucesso") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cadastro Concluído',
                                text: 'Cupom criado com sucesso!',
                            }).then(() => {
                                window.open(data.registro, '_blank');
                                window.location.href = "http://127.0.0.1:8000/dashboard";
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