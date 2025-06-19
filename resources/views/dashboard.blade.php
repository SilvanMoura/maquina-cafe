@extends('layouts/app')

@section('content')
<style>
    @media (min-width: 1366px) {
        .ajuste-cardbox {
            margin-top: -45% !important;
            min-height: 85vh !important;
        }
        .ajuste-statisc{
            margin-left:14vw;
        }
        .ajuste-status{
            margin-left: 14%; 
            width:84vw;
        }
    }
</style>
<!-- New Bem-vindos -->
<div id="content-bemv">
    <div class="bemv">Dashboard</div>
    <div></div>
</div>

<!--Action boxes-->
    <div class="ajuste-cardbox">
        <ul class="cardBox" style="margin-left: 14%;">
            <li class="card">
                <div class="grid-blak">
                    <a href="/clientes">
                        <div class="numbers">Clientes</div>
                    </a>
                </div>
                <a href="/clientes">
                    <div class="lord-icon02">
                        <i class='bx bx-user iconBx02'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/produtos">
                        <div class="numbers">Produtos</div>
                    </a>
                </div>
                <a href="/produtos">
                    <div class="lord-icon02">
                        <i class='bx bx-basket iconBx02'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/servicos">
                        <div class="numbers">Serviços</div>
                    </a>
                </div>
                <a href="/servicos">
                    <div class="lord-icon03">
                        <i class='bx bx-wrench iconBx03'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/os">
                        <div class="numbers N-tittle">Ordens</div>
                    </a>
                </div>
                <a href="/os">
                    <div class="lord-icon04">
                        <i class='bx bx-file iconBx04'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/maquinas">
                        <div class="numbers N-tittle">Máquinas</div>
                    </a>
                </div>
                <a href="/maquinas">
                    <div class="lord-icon05">
                        <i class='bx bx-cart-alt iconBx05'></i></span>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/garantias">
                        <div class="numbers">Garantias</div>
                    </a>
                </div>
                <a href="/garantias">
                    <div class="lord-icon06">
                        <i class="bx bx-receipt iconBx6"></i>
                    </div>
                </a>
            </li>

        </ul>
        <!--End-Action boxes-->
        
        <!-- Fim new widget right -->
        <div class="new-statisc ajuste-statisc">
            <div class="widget-box-new widbox-blak" style="height:100%">
                <div>
                    <h5 class="cardHeader">Dados Rápidos</h5>
                </div>

                <div class="new-bottons">

                    <!-- <a href="/clientes/adicionar" class="card tip-top" title="Add Clientes e Fornecedores">
                        <div><i class='bx bxs-group iconBx'></i></div>
                        <div>
                            <div class="cardName2">0</div>
                            <div class="cardName">Módulos</div>
                        </div>
                    </a> -->

                    <a href="/produtos/adicionar" class="card tip-top" title="Adicionar Produtos">
                        <div><i class='bx bxs-package iconBx2'></i></div>
                        <div>
                            <div class="cardName2">{{ $storesCount }}</div>
                            <div class="cardName">Lojas</div>
                        </div>
                    </a>
                    <a href="/produtos/adicionar" class="card tip-top" title="Adicionar Produtos">
                        <div><i class='bx bxs-package iconBx2'></i></div>
                        <div>
                            <div class="cardName2">{{ $posCount }}</div>
                            <div class="cardName">Pontos de Venda</div>
                        </div>
                    </a>

                    <a href="/os/adicionar" class="card tip-top" title="Adicionar serviços">
                        <div><i class='bx bxs-stopwatch iconBx3'></i></div>
                        <div>
                            <div class="cardName2">0</div>
                            <div class="cardName">Módulos Ativos</div>
                        </div>
                    </a>

                    <a href="/os/adicionar" class="card tip-top" title="Adicionar serviços">
                        <div><i class='bx bx-file iconBx3'></i></div>
                        <div>
                            <div class="cardName2">R$ {{ $todaySales }} - {{ $todayCount }}</div>
                            <div class="cardName">Vendas Hoje - Quantidade</div>
                        </div>
                    </a>

                    <a href="/os/adicionar" class="card tip-top" title="Adicionar OS">
                        <div><i class='bx bxs-spreadsheet iconBx4'></i></div>
                        <div>
                            <div class="cardName2">R$ {{ $sevenDaysSales }} - {{ $sevenDaysCount }}</div>
                            <div class="cardName">Vendas em 7 dias - Quantidade</div>
                        </div>
                    </a>
                    <a href="/os/adicionar" class="card tip-top" title="Adicionar OS">
                        <div><i class='bx bxs-spreadsheet iconBx4'></i></div>
                        <div>
                            <div class="cardName2">R$ {{ $thirtyDaysSales }} - {{ $thirtyDaysCount }}</div>
                            <div class="cardName">Vendas em 30 dias - Quantidade</div>
                        </div>
                    </a>
                    <a href="/os/adicionar" class="card tip-top" title="Adicionar OS">
                        <div><i class='bx bxs-spreadsheet iconBx4'></i></div>
                        <div>
                            <div class="cardName2">R$ {{ $allSales }} - {{ $allCount }}</div>
                            <div class="cardName">Todas as vendas - Quantidade</div>
                        </div>
                    </a>

                    <!-- <a href="/garantias" class="card tip-top" title="Adicionar garantia">
                        <div><i class='bx bxs-receipt iconBx6'></i></div>
                        <div>
                            <div class="cardName2">0</div>
                            <div class="cardName">Garantias</div>
                        </div>
                    </a> -->
                    <?php $diaRec = "VALOR_" . date('m') . "_REC";
                    $diaDes = "VALOR_" . date('m') . "_DES"; ?>

                </div>

            </div>
        </div>
        <div>

            <!-- Start Staus OS -->
            <div class="span12A ajuste-status">
                <div class="AAA">
                    <div class="widget-box0 widbox-blak">
                        <div>
                            <h5 class="cardHeader">Últimos Pix Recebidos</h5>
                        </div>
                        <div class="widget-content">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Valor</th>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Id transação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($allPix))
                                    @foreach($allPix as $allPix)
                                    <tr>
                                        <td>
                                            {{ $allPix->id }}
                                        </td>
                                        <td class="cli1">
                                            {{ $allPix->valor }}
                                        </td>
                                        <td>
                                            {{ $allPix->nome_remetente }}
                                        </td>
                                        <td>
                                            {{ $allPix->cpf_remetente }}
                                        </td>
                                        <td>
                                            {{ $allPix->id_mercado_pago }}
                                        </td>
                                        <td>
                                            <a href="{{ '/os/visualizar/'. $allPix->id }}" class="btn-nwe tip-top" title="Visualizar">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            @if( $allPix->status_os_id != 'Finalizado' )
                                            <a href="{{ '/os/imprimirOs/'. $allPix->id }}" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-printer bx-xs"></i></a>
                                            @else
                                            <a href="{{ '/os/entregaOs/'. $allPix->id }}" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-exit bx-xs"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6">Nenhum orçamento encontrado.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="widget-box0 widbox-blak">
                    <div>
                        <h5 class="cardHeader">Ordens de Serviço Em Aberto</h5>
                    </div>
                    <div class="widget-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Cliente</th>
                                    <th>Data Avaliação</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($dashboard['osServicos']))
                                @foreach($dashboard['osServicos'] as $osServicos)
                                <tr>
                                    <td>
                                        {{ $osServicos->id }}
                                    </td>
                                    <td class="cli1">
                                        {{ $osServicos->cliente_id }}
                                    </td>

                                    <td style="width:5vw;">
                                        {{ $osServicos->data_avaliacao }}
                                    </td>

                                    <td class="cli1">
                                        {{ $osServicos->status_os_id }}
                                    </td>
                                    <td class="cli1">
                                        R$ {{ $osServicos->valor_os }}
                                    </td>
                                    <td>
                                        <a href="{{ '/os/visualizar/'. $osServicos->id }}" class="btn-nwe tip-top" title="Visualizar">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ '/os/editar/'. $osServicos->id }}" class="btn-nwe5" title="Editar">
                                            <i class="bx bx-edit bx-xs"></i>
                                        </a>
                                        @if( $osServicos->status_os_id != 'Finalizado' )
                                        <a href="{{ '/os/imprimirOs/'. $osServicos->id }}" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-printer bx-xs"></i></a>
                                        @else
                                        <a href="{{ '/os/entregaOs/'. $osServicos->id }}" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-exit bx-xs"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5">Nenhuma OS em aberto.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fim Staus OS -->
    </div>

@endsection