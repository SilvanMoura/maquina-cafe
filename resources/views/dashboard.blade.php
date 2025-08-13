@extends('layouts/app')

@section('content')
<style>
    @media (min-width: 1366px) {
        .ajuste-cardbox {
            margin-top: -45% !important;
            min-height: 85vh !important;
        }
        .ajuste-statisc{
            margin-left: 7vw;
        }
        .ajuste-status{
            margin-left: 14%; 
            width:84vw;
        }
        .atalhos{
            margin-left: 14%;
        }
    }

    @media (max-width: 485px) {
        .ajuste-statisc {
            width: 100%;
            margin-top: 5%;
        }
        
    }
</style>

<!--Action boxes-->
    <div class="ajuste-cardbox">
        <ul class="cardBox atalhos">
            <li class="card">
                <div class="grid-blak">
                    <a href="/usuarios">
                        <div class="numbers">Usuários</div>
                    </a>
                </div>
                <a href="/usuarios">
                    <div class="lord-icon02">
                        <i class='bx bx-user iconBx02'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/lojas">
                        <div class="numbers">Lojas</div>
                    </a>
                </div>
                <a href="/lojas">
                    <div class="lord-icon02">
                        <i class='bx bx-basket iconBx02'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/controle">
                        <div class="numbers">Controle Remoto</div>
                    </a>
                </div>
                <a href="/controle">
                    <div class="lord-icon03">
                        <i class='bx bx-wrench iconBx03'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/modulos">
                        <div class="numbers N-tittle">Modulos</div>
                    </a>
                </div>
                <a href="/modulos">
                    <div class="lord-icon04">
                        <i class='bx bxs-package iconBx04'></i>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/vendas">
                        <div class="numbers N-tittle">Vendas</div>
                    </a>
                </div>
                <a href="/vendas">
                    <div class="lord-icon05">
                        <i class='bx bx-cart-alt iconBx05'></i></span>
                    </div>
                </a>
            </li>

            <li class="card">
                <div class="grid-blak">
                    <a href="/cupons">
                        <div class="numbers">Cupons</div>
                    </a>
                </div>
                <a href="/cupons">
                    <div class="lord-icon06">
                        <i class="bx bx-receipt iconBx6"></i>
                    </div>
                </a>
            </li>

        </ul>
        <!--End-Action boxes-->
        
        <!-- Fim new widget right -->
        <div class="new-statisc ajuste-statisc">
            <div class="widget-box-new widbox-blak ajuste-statisc" style="height:100%">
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

                    <a href="/lojas" class="card tip-top" title="Ver Lojas">
                        <div><i class='bx bxs-package iconBx2'></i></div>
                        <div>
                            <div class="cardName2">{{ $storesCount }}</div>
                            <div class="cardName">Lojas</div>
                        </div>
                    </a>

                    <a href="/modulos/online" class="card tip-top" title="Ver Modulos">
                        <div><i class='bx bxs-stopwatch iconBx3'></i></div>
                        <div>
                            <div class="cardName2">{{ $countOnline }}</div>
                            <div class="cardName">Módulos Ativos</div>
                        </div>
                    </a>

                    <a href="vendas" class="card tip-top" title="Ver Vendas">
                        <div><i class='bx bx-cart iconBx3'></i></div>
                        <div>
                            <div class="cardName2">R$ {{ $todaySales }} - {{ $todayCount }}</div>
                            <div class="cardName">Vendas Hoje - Quantidade</div>
                        </div>
                    </a>

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