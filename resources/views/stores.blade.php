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

        <div class="widget-box">
            <h5 style="padding: 3px 0"></h5>
            <div class="widget-content nopadding tab-content scrollable-container">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                        </tr>
                    </thead>
                    <tbody>


                        @if($storesData->count() > 0)
                        @foreach ($storesData as $r)
                        <tr>

                            <td style="width:10%;">{{ $r['id'] }}</td>
                            <td style="width:35%;"><a>{{ $r['name'] }}</a></td>
                            <td style="width:50%;"><a>{{ $r['location']['address_line'] }}</a></td>

                            <td style="width:55%;">
                                <a href="{{ '/os/editar/'. $r['id'] }}" class="btn-nwe5" title="Editar"><i class="bx bx-edit bx-xs"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6">Nenhum Loja Cadastrada</td>
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