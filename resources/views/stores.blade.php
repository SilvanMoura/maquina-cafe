@extends('layouts/app')

@section('content')

<style>
    select {
        width: 70px;
    }

    .scrollable-container {
        height: 400px;
        /* Altura fixa da div */
        overflow-y: auto;
        /* Adiciona rolagem vertical quando necessário */
    }
</style>
<div style="height: 90vh; width: 99vw;">
    <div class="new122" style="margin: 1% 1% 0 7%;">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-user"></i>
            </span>
            <h5>Lojas</h5>
        </div>
        <!-- php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCliente')) { ?> -->
        <!-- <div class="flexxn" style="background-color:red; display: block;">
            <div style="background-color:blue; display: flex; justify-content:space-between;">

                <div>
                    <a href="os/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                            Ordem de Serviço
                        </span>
                    </a>
                </div>

                <div>
                    <label id="search">
                        Pesquisar
                        <input type="search" id="searchInput" class="" placeholder="Id OS ou Nome Cliente" aria-controls="tabela">
                    </label>
                </div>

            </div>
        </div> -->

        <div class="flexxn" style="display:block;">
            <div style="display: block; flex-direction:column;">

                <div>
                    <a href="os/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                            Nova Loja
                        </span>
                    </a>
                </div>

                <div>
                    <label id="search">
                        Pesquisar
                        <input type="search" id="searchInput" class="" placeholder="Nome da Loja" aria-controls="tabela">
                    </label>
                </div>

            </div>
        </div>

        <!-- <div class="flexxn" style="background-color:red; display: block;">
            <a href="os/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                    Ordem de Serviço
                </span>
            </a>
        </div>
        <div class="flexxn" style="background-color:red; display: block;">
            <label id="search">
                Pesquisar
                <input type="search" id="searchInput" class="" placeholder="Id OS ou Nome Cliente" aria-controls="tabela">
            </label>
        </div> -->
        <!-- ?php } ?> -->

        <div class="widget-box">
            <h5 style="padding: 3px 0"></h5>
            <div class="widget-content nopadding tab-content scrollable-container">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Máquina</th>
                            <th>Status</th>
                            <th>Tipo</th>
                            <th>Avaliação</th>
                            <th>Valor</th>
                            <th>Data Entrega</th>
                            <th>Garantia</th>
                        </tr>
                    </thead>
                    <tbody>


                        @if(1 > 0)
                        
                        <tr>

                            <td style="width:5%;">  </td>
                            <td style="width:25%;"><a href=""> </a></td>
                            <td style="width:12%;"><a href=""> </a></td>
                            <td style="width:10%;"><a href=""> </a></td>
                            <td style="width:6%;"><a href=""> </a></td>
                            <td style="width:6%;"><a href=""> </a></td>
                            <td style="width:7%;"><a href="">R$  </a></td>

                            <td style="width:8%;"><a href=""> </a></td>
                            @if( 1 != null)
                            <td style="width:9%;"><a href="">  </a></td>
                            @else
                            <td style="width:9%;"><a href="">sem garantia</a></td>
                            @endif
                            <td style="width:12%;">
                                <a href=" '/os/visualizar/'. " class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>
                                <a href=" '/os/editar/'. " class="btn-nwe5" title="Editar"><i class="bx bx-edit bx-xs"></i></a>
                                
                                <a href=" '/os/imprimirOs/'. " class="btn-nwe3" title="Imprimir OS"><i class="bx bx-printer bx-xs"></i></a>
                                
                            </td>
                        </tr>
                        
                        @else
                        <tr>
                            <td colspan="6">Nenhum OS Cadastrado</td>
                        </tr>
                        @endif


                    </tbody>
                </table>
            </div>
        </div>
        <!-- <php echo $this->pagination->create_links(); ?> -->

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // $('#searchInput').on('input', function() {
        //     performSearch();
        // });

        $('#searchInput').on('keydown', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        function performSearch() {

            var searchTerm = $('#searchInput').val();

            // Selecione o elemento `tbody` dentro do widget
            var tableBody = $('.widget-box .widget-content.nopadding.tab-content.scrollable-container #tabela tbody');

            $.ajax({
                type: 'POST',
                url: '/search/os',
                data: {
                    search: searchTerm
                },
                success: function(data) {
                    // Limpe a tabela antes de inserir novos dados
                    tableBody.empty();

                    // Declare a variável `item` fora do loop
                    var item;

                    // Crie linhas para cada item da lista de resultados
                    $.each(data, function(index, item) {
                        var row = '<tr>';
                        row += '<td style="width:5%;">' + item.id + '</td>';
                        row += '<td style="width:25%;"><a href="os/visualizar/' + item.id + '">' + item.cliente_id + '</a></td>';
                        row += '<td style="width:12%;"><a href="os/visualizar/' + item.id + '">' + item.maquina_id + '</a></td>';
                        row += '<td style="width:10%;"><a href="os/visualizar/' + item.id + '">' + item.status_os_id + '</a></td>';
                        row += '<td style="width:6%;"><a href="os/visualizar/' + item.id + '">' + item.operacao_os_id + '</a></td>';
                        row += '<td style="width:6%;"><a href="os/visualizar/' + item.id + '">' + item.data_avaliacao + '</a></td>';
                        row += '<td style="width:7%;"><a href="os/visualizar/' + item.id + '">R$ ' + item.valor_os + '</a></td>';
                        row += '<td style="width:8%;"><a href="os/visualizar/' + item.id + '">' + item.data_entrega + '</a></td>';

                        if (item.garantia != null) {
                            row += '<td style="width:9%;"><a href="os/visualizar/' + item.id + '">' + item.garantiaFinalData + '</a></td>';
                        } else {
                            row += '<td style="width:9%;"><a href="os/visualizar/' + item.id + '">sem garantia</a></td>';
                        }

                        row += '<td style="width:12%;">';
                        row += '<a href="os/visualizar/' + item.id + '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                        row += '<a href="os/editar/' + item.id + '" class="btn-nwe5" title="Editar"><i class="bx bx-edit bx-xs"></i></a>';
                        row += '<a href="os/imprimirOs/' + item.id + '" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-printer bx-xs"></i></a>';
                        row += '<a href="os/entregaOs/' + item.id + '" class="btn-nwe3" title="Entrega OS"><i class="bx bx-exit bx-xs"></i></a>';
                        row += '</td>';
                        row += '</tr>';

                        // Adicione a linha à tabela
                        tableBody.append(row);
                    });
                },
                error: function(erro) {
                    console.error('Erro:', erro);
                }
            });

        }
    });
</script>

@endsection