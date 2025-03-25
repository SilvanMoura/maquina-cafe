@extends('layouts/app')

@section('content')
<!-- New Bem-vindos -->
<div id="content-bemv">
    <div class="bemv">Dashboard</div>
    <div></div>
</div>

<!--Action boxes-->
<ul class="cardBox">
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

<div class="row-fluid" style="margin-left: 8%; margin-top: 0; display: flex">
    <div class="Sspan12">

        <!-- New widget right -->
        <div class="new-statisc">
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
                            <div class="cardName2">0</div>
                            <div class="cardName">Vendas</div>
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
                            <div class="cardName2">0</div>
                            <div class="cardName">Módulos Total</div>
                        </div>
                    </a>

                    <a href="/os/adicionar" class="card tip-top" title="Adicionar OS">
                        <div><i class='bx bxs-spreadsheet iconBx4'></i></div>
                        <div>
                            <div class="cardName2">0</div>
                            <div class="cardName">Cupons</div>
                        </div>
                    </a>

                    <a href="/maquinas-adicionar" class="card tip-top" title="Adicionar Máquina">
                        <div><i class='bx bxs-cart-alt iconBx5'></i></div>
                        <div>
                            <div class="cardName2">0</div>
                            <div class="cardName">Lojas</div>
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
    </div>
</div>
<!-- Fim new widget right -->

<script type="text/javascript">
    if (window.outerWidth > 2000) {
        Chart.defaults.font.size = 15;
    };
    if (window.outerWidth < 2000 && window.outerWidth > 1367) {
        Chart.defaults.font.size = 11;
    };
    if (window.outerWidth < 1367 && window.outerWidth > 480) {
        Chart.defaults.font.size = 9.5;
    };
    if (window.outerWidth < 480) {
        Chart.defaults.font.size = 8.5;
    };

    var ctx = document.getElementById('myChart').getContext('2d');
    var StatusOS = document.getElementById('statusOS').getContext('2d');

    var myChart = new Chart(ctx, {
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                    label: 'Receita Líquida',
                    data: [<?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>,
                        <?php echo (0 - 0); ?>
                    ],

                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderRadius: 15,
                },

                {
                    label: 'Receita Bruta',
                    data: [<?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>
                    ],

                    backgroundColor: 'rgba(255, 206, 86, 0.5)',
                    borderRadius: 15,
                },

                {
                    label: 'Despesas',
                    data: [<?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>,
                        <?php echo (1); ?>
                    ],

                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderRadius: 15,
                },

                {
                    label: 'Inadimplência',
                    data: [<?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>,
                        <?php echo (2); ?>
                    ],

                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderRadius: 15,
                }
            ]

        },
        // configuração
        type: 'bar',
        options: {
            locale: 'pt-BR',
            scales: {
                y: {
                    ticks: {
                        callback: (value, index, values) => {
                            return new Intl.NumberFormat('pt-BR', {
                                style: 'currency',
                                currency: 'BRL',
                                maximumSignificantDidits: 1
                            }).format(value);
                        }
                    }
                },
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Meses'
                    }
                }
            },

            plugins: {
                tooltip: {
                    callbacks: {
                        beforeTitle: function(context) {
                            return 'Referente ao mês de';
                        }
                    }
                },

                legend: {
                    position: "bottom",
                    labels: {
                        usePointStyle: true,
                    }
                }
            }
        }
    });

    var myChart = new Chart(statusOS, {
        data: {
            labels: [
                'Receita total', 'Receita pendente',
                'Previsto em caixa', 'Despesa total',
                'Despesa pendente', 'Previsto a entrar'
            ],
            datasets: [{
                label: 'Total',
                data: [
                    <?php echo ('0.00'); ?>,
                    <?php echo ('0.00'); ?>,
                    <?php echo ('0'); ?>,
                    <?php echo ('0.00'); ?>,
                    <?php echo ('0.00'); ?>,
                    <?php echo ('0'); ?>
                ],

                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderWidth: 1
            }]
        },

        // configuração
        type: 'polarArea',
        options: {
            locale: 'pt-BR',
            scales: {
                r: {
                    ticks: {
                        callback: (value, index, values) => {
                            return new Intl.NumberFormat('pt-BR', {
                                style: 'currency',
                                currency: 'BRL',
                                maximumSignificantDidits: 1
                            }).format(value);
                        }
                    },
                    beginAtZero: true,
                }
            },
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        usePointStyle: true,

                    }
                }
            }
        }
    });

    function responsiveFonts() {
        myChart.update();
    }
</script>
<!-- php  }
} -->
</div>
</div>

<!-- Start Staus OS -->
<div class="span12A" style="margin-left: 8%; width:90vw">
    <div class="AAA">
        <div class="widget-box0 widbox-blak">
            <div>
                <h5 class="cardHeader">Orçamentos</h5>
            </div>
            <div class="widget-content">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cod.</th>
                            <th>Nome</th>
                            <th>Data Avaliação</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dashboard['osOrcamento']))
                        @foreach($dashboard['osOrcamento'] as $osOrcamento)
                        <tr>
                            <td>
                                {{ $osOrcamento->id }}
                            </td>
                            <td class="cli1">
                                {{ $osOrcamento->cliente_id }}
                            </td>
                            <td>
                                {{ $osOrcamento->data }}
                            </td>
                            <td>
                                {{ $osOrcamento->status_os_id }}
                            </td>
                            <td>
                                <a href="{{ '/os/visualizar/'. $osOrcamento->id }}" class="btn-nwe tip-top" title="Visualizar">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ '/os/editar/'. $osOrcamento->id }}" class="btn-nwe5" title="Editar">
                                    <i class="bx bx-edit bx-xs"></i>
                                </a>
                                @if( $osOrcamento->status_os_id != 'Finalizado' )
                                <a href="{{ '/os/imprimirOs/'. $osOrcamento->id }}" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-printer bx-xs"></i></a>
                                @else
                                <a href="{{ '/os/entregaOs/'. $osOrcamento->id }}" class="btn-nwe3" title="Imprimir OS"><i class="bx bx-exit bx-xs"></i></a>
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
<!-- Fim Staus OS -->
<script src="{{ asset('js/jquery.validate.js') }}"></script>

@endsection