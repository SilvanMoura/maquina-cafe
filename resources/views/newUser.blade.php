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
<div class="ajuste" style="height: 92vh;">
    <div class="row-fluid ajuste-container" style="margin: 0% 0% 0 7%; width: 91vw;">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <h5>Cadastrar Novo Usuário</h5>
                </div>

                <div class="alert alert-danger hide" id="error-message"></div>

                <form id="formUser" class="form-horizontal">
                    @csrf
                    <div class="widget-content nopadding tab-content">
                        <div class="span6">
                            <div class="control-group">
                                <label for="cpfCnpjUser" class="control-label">CPF/CNPJ</label>
                                <div class="controls">
                                    <input id="cpfCnpjUser" class="cpfcnpj" type="text" name="cpfCnpjUser" value="" />
                                    <button id="buscar_info_cnpj" class="btn btn-xs" type="button">Buscar(CNPJ)</button>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="nameUser" class="control-label required">Nome</label>
                                <div class="controls">
                                    <input id="nameUser" type="text" name="nameUser" value="" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="emailUser" class="control-label required">Email</label>
                                <div class="controls">
                                    <input id="emailUser" type="text" name="emailUser" value="" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="contatoUser" class="control-label required">Contato</label>
                                <div class="controls">
                                    <input id="contatoUser" type="text" name="contatoUser" value="" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="pixUser" class="control-label required">Chave PIX para repasses</label>
                                <div class="controls">
                                    <input id="pixUser" type="text" name="pixUser" value="" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="modulo" class="control-label">Modulo MCCF - </label>
                                <div class="controls">
                                    <select id="modulo" name="modulo" class="form-control">
                                        @foreach($modules as $f)
                                        <option value="{{ $f->id }}">{{ $f->modulo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="span6">

                            <div class="control-group" class="control-label">
                                <label for="cep" class="control-label">CEP</label>
                                <div class="controls">
                                    <input id="cep" type="text" name="cep" value="" />
                                </div>
                            </div>
                            <div class="control-group" class="control-label">
                                <label for="endereco" class="control-label">Rua</label>
                                <div class="controls">
                                    <input id="endereco" type="text" name="endereco" value="" />
                                </div>
                            </div>
                            <div class="control-group" class="control-label">
                                <label for="bairro" class="control-label">Bairro</label>
                                <div class="controls">
                                    <input id="bairro" type="text" name="bairro" value="" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="complemento" class="control-label">Número</label>
                                <div class="controls">
                                    <input id="complemento" type="text" name="complemento" value="" />
                                </div>
                            </div>
                            <div class="control-group" class="control-label">
                                <label for="cidade" class="control-label">Cidade</label>
                                <div class="controls">
                                    <input id="cidade" type="text" name="cidade" value="" />
                                </div>
                            </div>
                            <div class="control-group" class="control-label">
                                <label for="estado" class="control-label">Estado</label>
                                <div class="controls">
                                    <select id="estado" name="estado">
                                        <option value="">Selecione...</option>
                                    </select>
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

        $.getJSON("{{ asset('json/estados.json') }}", function(data) {
            for (i in data.estados) {
                $('#estado').append(new Option(data.estados[i].nome, data.estados[i].sigla));
            }
            var curState = "{{ old('estado') }}";
            if (curState) {
                $("#estado option[value=" + curState + "]").prop("selected", true);
            }
        });

        $("#cpfCnpjStore").focus();
        $('#formStore').validate({
            rules: {
                nameStore: {
                    required: true
                },
                cpfCnpjStore: {
                    required: true
                }
            },
            messages: {
                nameStore: {
                    required: 'Campo Requerido.'
                },
                cpfCnpjStore: {
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
            if ($("#formUser").valid()) {
                var dados = $("#formUser").serialize();

                $(this).addClass('disabled');
                $('#progress-acessar').removeClass('hide');

                // Requisição AJAX
                $.ajax({
                    type: "POST",
                    url: "https://srv981758.hstgr.cloud/usuarios/adicionar",
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Usuário criado com sucesso") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cadastro Concluído',
                                text: 'Usuário criado com sucesso!',
                            }).then(() => {
                                window.location.href = "https://srv981758.hstgr.cloud/dashboard";
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