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
            margin-top: -46%
        }
    }
</style>
<div class="ajuste-container" style="height: 100vh; width: 99vw; margin-bottom:10%;">
    <div class="new122" style="margin: 1% 1% 0 7%;">
        <ul class="nav nav-tabs">
            <li><a data-toggle="tab" href="#tab1">Hoje</a></li> <!-- class="active" -->
            <li><a data-toggle="tab" href="#tab2">7 dias</a></li>
            <li><a data-toggle="tab" href="#tab3">30 dias</a></li>
            <li><a data-toggle="tab" href="#tab4">Todas</a></li>
        </ul>
    </div>
    <div class="widget-content tab-content new122" style="margin: 1% 1% 0 7%;">
        <div id="tab1" class="tab-pane active" style="min-height: 300px">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Módulo</th>
                        <th>Valor</th>
                        <th>Nome Comprador</th>
                        <th>Documento</th>
                        <th>Nome Loja</th>
                        <th>Ponto de Venda</th>
                        <th>Data da Venda</th>
                    </tr>
                </thead>
                <tbody>
                    @if($paymentsToday->count() > 0)
                    @foreach ($paymentsToday as $r)
                    <tr>
                        <td style="width:5%">{{ $r->id }}</td>
                        <td style="width:5%">{{ $r->external_reference }}</td>
                        <td style="width:5%">{{ $r->transaction_amount }}</td>
                        <td style="width:15%">{{ $r->receipt->nome_remetente }}</td>
                        <td style="width:15%">{{ $r->receipt->cpf_remetente }}</td>
                        <td style="width:15%">{{ $r->store_name }}</td>
                        <td style="width:15%">{{ $r->pos_name }}</td>
                        <td style="width:15%">{{ $r->created_at }}</td>

                        <td>
                            <a href="os/editar/" class="btn btn-info tip-top" title="Editar OS">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6">Nenhuma Venda Encontrada</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!--Tab 2-->
        <div id="tab2" class="tab-pane" style="max-height: auto">
            <!-- ?php if (!$results) { ?> -->
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Módulo</th>
                        <th>Valor</th>
                        <th>Nome Comprador</th>
                        <th>Documento</th>
                        <th>Nome Loja</th>
                        <th>Ponto de Venda</th>
                        <th>Data da Venda</th>
                    </tr>
                </thead>
                <tbody>
                    @if($paymentsSevenDays->count() > 0)
                    @foreach ($paymentsSevenDays as $r)
                    <tr>
                        <td style="width:5%">{{ $r->id }}</td>
                        <td style="width:5%">{{ $r->external_reference }}</td>
                        <td style="width:5%">{{ $r->transaction_amount }}</td>
                        <td style="width:15%">{{ $r->receipt->nome_remetente }}</td>
                        <td style="width:15%">{{ $r->receipt->cpf_remetente }}</td>
                        <td style="width:15%">{{ $r->store_name }}</td>
                        <td style="width:15%">{{ $r->pos_name }}</td>
                        <td style="width:15%">{{ $r->created_at }}</td>

                        <td>
                            <a href="os/editar/" class="btn btn-info tip-top" title="Editar OS">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6">Nenhuma Venda Encontrada</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!--Tab 3-->
        <div id="tab3" class="tab-pane" style="min-height: 300px">
            <!-- ?php if (!$result_vendas) { ?> -->
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Módulo</th>
                        <th>Valor</th>
                        <th>Nome Comprador</th>
                        <th>Documento</th>
                        <th>Nome Loja</th>
                        <th>Ponto de Venda</th>
                        <th>Data da Venda</th>
                    </tr>
                </thead>
                <tbody>
                    @if($paymentsLast30Days->count() > 0)
                    @foreach ($paymentsLast30Days as $r)
                    <tr>
                        <td style="width:5%">{{ $r->id }}</td>
                        <td style="width:5%">{{ $r->external_reference }}</td>
                        <td style="width:5%">{{ $r->transaction_amount }}</td>
                        <td style="width:15%">{{ $r->receipt->nome_remetente }}</td>
                        <td style="width:15%">{{ $r->receipt->cpf_remetente }}</td>
                        <td style="width:15%">{{ $r->store_name }}</td>
                        <td style="width:15%">{{ $r->pos_name }}</td>
                        <td style="width:15%">{{ $r->created_at }}</td>

                        <td>
                            <a href="os/editar/" class="btn btn-info tip-top" title="Editar OS">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6">Nenhuma Venda Encontrada</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!--Tab 4-->
        <div id="tab4" class="tab-pane" style="min-height: 300px">
            <!-- ?php if (!$result_vendas) { ?> -->
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Módulo</th>
                        <th>Valor</th>
                        <th>Nome Comprador</th>
                        <th>Documento</th>
                        <th>Nome Loja</th>
                        <th>Ponto de Venda</th>
                        <th>Data da Venda</th>
                    </tr>
                </thead>
                <tbody>
                    @if($allPayments->count() > 0)
                    @foreach ($allPayments as $r)
                    <tr>
                        <td style="width:5%">{{ $r->id }}</td>
                        <td style="width:5%">{{ $r->external_reference }}</td>
                        <td style="width:5%">{{ $r->transaction_amount }}</td>
                        <td style="width:15%">{{ $r->receipt->nome_remetente }}</td>
                        <td style="width:15%">{{ $r->receipt->cpf_remetente }}</td>
                        <td style="width:15%">{{ $r->store_name }}</td>
                        <td style="width:15%">{{ $r->pos_name }}</td>
                        <td style="width:15%">{{ $r->created_at }}</td>

                        <td>
                            <a href="os/editar/" class="btn btn-info tip-top" title="Editar OS">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6">Nenhuma Venda Encontrada</td>
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