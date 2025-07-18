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
            <h5>Cupons</h5>
        </div>

        <div class="flexxn" style="display:block;">
            <div style="display: block; flex-direction:column;">

                <div>
                    <a href="cupons/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                            Novo Cupom
                        </span>
                    </a>
                </div>

                <div>
                    <label id="search">
                        Pesquisar
                        <input type="search" id="searchInput" class="" placeholder="Nome do Cupom" aria-controls="tabela">
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
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Destino</th>
                            <th>Data Criação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($coupons->count() > 0)
                        @foreach ($coupons as $r)
                        <tr>
                            <td style="width:15%;">{{ $r->id }}</td>
                            <td style="width:15%;">{{ $r->name }}<a></a></td>
                            <td style="width:15%;">{{ $r->value }}</td>
                            <td style="width:15%;"><a>{{ $r->status }}</a></td>
                            <td style="width:15%;">{{ $r->telefone }}</td>
                            <td style="width:15%;"><a>{{ $r->created_at }}</a></td>

                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6">Nenhum Módulo Cadastrado</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

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