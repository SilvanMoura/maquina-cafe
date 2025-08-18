@extends('layouts/app')

@section('content')

<style>
    select {
        width: 70px;
    }

    .scrollable-container {
        height: 400px;
        overflow-y: auto;
    }

    .select2-container {
        z-index: 99999;
    }

    .select2-search__field {
        width: 100% !important;
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
    <div class="new122" style="margin: 1% 1% 0 7%;">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-user"></i>
            </span>
            <h5>Usuários</h5>
        </div>
        <!-- php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCliente')) { ?> -->
        <a href="/usuarios/adicionar" class="button btn btn-mini btn-success open-modal-create" style="max-width: 165px">
            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                Usuário
            </span>
        </a>
        <!-- ?php } ?> -->

        <div class="widget-box">
            <h5 style="padding: 3px 0"></h5>
            <div class="widget-content nopadding tab-content scrollable-container">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if($allUsers->count() > 0)
                        @foreach ($allUsers as $r)
                        <tr>
                            <td style="width:5%;">{{ $r->id }}</td>
                            <td style="width:20%;">{{ $r->name }}</td>
                            <td style="width:20%;">{{ $r->email }}</td>
                            <td style="width:6%;">
                                <a href="#modal-edit" role="button" data-id="{{ $r->id }}" data-nome="{{ $r->name }}" data-email="{{ $r->email }}" data-level="{{ $r->level }}" data-toggle="modal" class="btn-nwe3 open-edit-user" title="Editar Usuário"><i class="bx bx-edit bx-xs"></i></a>
                                <a href="#modal-delete" role="button" data-id="{{ $r->id }}" data-toggle="modal" class="btn-nwe4 open-modal-delete" title="Excluir Usuário"><i class="bx bx-trash-alt bx-xs"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6">Nenhum Usuário Cadastrado</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
        <!-- <php echo $this->pagination->create_links(); ?> -->

        <!-- Modal Estoque -->
        <div id="edit-user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form id="formEdit">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close close-btn" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 id="myModalLabel"><i class="fas fa-plus-square"></i> Editar Usuário</h5>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="idUser" class="idUser" name="id" value="" />
                    <div class="control-group">
                        <label for="name" class="control-label">Nome</label>
                        <div class="controls">
                            <input id="name" type="text" name="name" value="" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="email" class="control-label">E-mail</label>
                        <div class="controls">
                            <input id="email" type="text" name="email" value="" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="level" class="control-label">Nível de acesso</label>
                        <div class="controls">
                            <input id="level" type="text" name="level" value="" />
                        </div>
                    </div>

                    <div class="modal-footer" style="display:flex;justify-content: center">
                        <button id="btnUpdate" class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                        <button type="button" class="button btn btn-warning close-btn" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                    </div>
            </form>
        </div>

    </div>

    <div id="delete-user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form id="formDelete">
            @csrf
            <div class="modal-header">
                <button type="button" class="close close-delete" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel"><i class="fas fa-trash-alt"></i> Excluir Usuário</h5>
            </div>

            <div class="modal-body">
                <input type="hidden" id="idUser-delete" class="idUser-delete" name="id" value="" />
                <h5 style="text-align: center">Deseja realmente excluir o usuário <span id="id-delete"></span>?</h5>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button type="button" class="button btn btn-warning close-delete" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                <button id="btnDelete" class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
            </div>
        </form>
    </div>

    <div id="create-user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form id="formCreate">
            @csrf
            <div class="modal-header">
                <button type="button" class="close close-create" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel"><i class="fas fa-plus-square"></i> Criar Usuário</h5>
            </div>

            <div class="modal-body">

                <div class="control-group">
                    <label for="name" class="control-label">Nome</label>
                    <div class="controls">
                        <input id="name" type="text" name="name" value="" />
                    </div>
                </div>

                <div class="control-group">
                    <label for="email" class="control-label">Email</label>
                    <div class="controls">
                        <input id="email" type="text" name="email" value="" />
                    </div>
                </div>

                <div class="control-group">
                    <label for="level" class="control-label">Nível de Acesso</label>
                    <div class="controls">
                        <input id="level" type="text" name="level" value="3" />
                    </div>
                </div>

                <div class="modal-footer" style="display:flex;justify-content: center">
                    <button id="btnCreate" class="button btn btn-success"><span class="button__icon"><i class="bx bx-plus"></i></span><span class="button__text2">Adicionar</span></button>
                    <button type="button" class="button btn btn-warning close-btn close-create" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                </div>
            </div>
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
        $('#Manufacturer').select2({
            tags: true
        });

        $('#Manufacturers').select2({
            tags: true
        });

        $('.open-edit-user').on('click', function(event) {
            var modal = document.getElementById("edit-user");
            modal.classList.remove("hide", "fade");

            var id = $(this).attr('data-id');
            var name = $(this).attr('data-nome');
            var email = $(this).attr('data-email');
            var level = $(this).attr('data-level');

            $('#idUser').val(id);
            $('#name').val(name);
            $('#email').val(email);
            $('#level').val(level);
        });

        $('.open-modal-delete').on('click', function(event) {
            var modal = document.getElementById("delete-user");
            modal.classList.remove("hide", "fade");

            var id = $(this).attr('data-id');

            $('#idUser-delete').val(id);
            $('#id-delete').text(id);
        });

        /* $('.open-modal-create').on('click', function(event) {
            var modal = document.getElementById("create-user");
            modal.classList.remove("hide", "fade");
        });
 */
        $('.close-btn').on('click', function(event) {
            var modal = document.getElementById("edit-user");
            modal.classList.add("hide", "fade");
        })

        $('.close-create').on('click', function(event) {
            var modal = document.getElementById("create-user");
            modal.classList.add("hide", "fade");
        })

        $('.close-delete').on('click', function(event) {
            var modal = document.getElementById("delete-user");
            modal.classList.add("hide", "fade");
        })

        $('#btnCreate').on('click', function(e) {
            e.preventDefault();

            // Validação do formulário usando o plugin validate
            if ($("#formCreate").valid()) {

                // Serializar dados incluindo o valor selecionado
                var dados = $("#formCreate").serialize();

                // Requisição AJAX
                $.ajax({
                    type: "POST",
                    url: "https://8a7444e0a6fd.ngrok-free.app/usuarios/adicionar",
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Usuário registrado com sucesso") {
                            var modal = document.getElementById("create-user");
                            modal.classList.add("hide", "fade");

                            Swal.fire({
                                icon: 'success',
                                title: 'Cadastro Concluído',
                                text: 'Usuário registrado com sucesso!',
                            }).then(() => {
                                window.location.href = "https://8a7444e0a6fd.ngrok-free.app/dashboard";
                            });
                        } else {
                            var modal = document.getElementById("create-user");
                            modal.classList.add("hide", "fade");

                            Swal.fire({
                                icon: 'error',
                                title: 'Erro na criação',
                                text: data.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var modal = document.getElementById("create-user");
                        modal.classList.add("hide", "fade");

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro na criação',
                            text: xhr.responseJSON.message,
                        });
                    },
                    complete: function() {
                        // Limpar qualquer indicação visual de loading, se necessário
                    }
                });
            }
        })

        $('#btnDelete').on('click', function(e) {
            e.preventDefault();

            // Validação do formulário usando o plugin validate
            if ($("#formDelete").valid()) {

                var dados = $("#formDelete").serializeArray();

                $(this).addClass('disabled');
                $('#progress-acessar').removeClass('hide');

                // Requisição AJAX
                $.ajax({
                    type: "DELETE",
                    url: "https://8a7444e0a6fd.ngrok-free.app/usuarios/delete/" + dados[1]['value'],
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Usuário excluido com sucesso") {
                            var modal = document.getElementById("delete-user");
                            modal.classList.add("hide", "fade");
                            Swal.fire({
                                icon: 'success',
                                title: 'Exclusão Concluída',
                                text: 'Usuário excluido com sucesso!',
                            }).then(() => {
                                window.location.href = "https://8a7444e0a6fd.ngrok-free.app/dashboard";
                            });
                        } else {
                            var modal = document.getElementById("delete-user");
                            modal.classList.add("hide", "fade");

                            Swal.fire({
                                icon: 'error',
                                title: 'Erro na exclusão',
                                text: data.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var modal = document.getElementById("delete-user");
                        modal.classList.add("hide", "fade");

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro na exclusão',
                            text: xhr.responseJSON.message,
                        });
                    },
                    complete: function() {
                        // Limpar qualquer indicação visual de loading, se necessário
                    }
                });
            }
        })

        $('#btnUpdate').on('click', function(e) {
            e.preventDefault();

            // Validação do formulário usando o plugin validate
            if ($("#formEdit").valid()) {

                var dados = $("#formEdit").serializeArray();

                // Requisição AJAX
                $.ajax({
                    type: "PUT",
                    url: "https://8a7444e0a6fd.ngrok-free.app/usuarios/atualizar/" + dados[1]['value'],
                    data: dados,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.message === "Usuário alterado com sucesso") {
                            var modal = document.getElementById("edit-user");
                            modal.classList.add("hide", "fade");
                            Swal.fire({
                                icon: 'success',
                                title: 'Alteração Concluído',
                                text: 'Usuário alterado com sucesso!',
                            }).then(() => {
                                window.location.href = "https://8a7444e0a6fd.ngrok-free.app/dashboard";
                            });
                        } else {
                            var modal = document.getElementById("edit-user");
                            modal.classList.add("hide", "fade");
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro na alteração',
                                text: data.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {

                        var modal = document.getElementById("edit-user");
                        modal.classList.add("hide", "fade");

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