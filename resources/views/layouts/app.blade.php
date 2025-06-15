<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Café da Fazenda</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/cafe-fazenda-logo-sistema.png') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap-responsive.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/matrix-style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/matrix-media.css') }}" />
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}" />

    <link rel="stylesheet" href=" {{asset('css/tema-white.css')}} " />

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;700&display=swap' rel='stylesheet' type='text/css'>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script type="text/javascript" src="{{ asset('js/jquery-1.12.4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/shortcut.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/funcoesGlobal.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/sweetalert.min.js') }}"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script type="text/javascript">
        shortcut.add("escape", function() {
            location.href = '/';
        });
        // Add other shortcut functions here...
        window.BaseUrl = "/";
    </script>

    <script language="javascript" type="text/javascript" src="{{ asset('js/dist/jquery.jqplot.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dist/plugins/jqplot.pieRenderer.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dist/plugins/jqplot.donutRenderer.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar.min.js')}}"></script>
    <script src="{{ asset('js/fullcalendar/locales/pt-br.js') }}"></script>

    <link href="{{ asset('css/fullcalendar.min.css') }}" rel='stylesheet' />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/dist/jquery.jqplot.min.css') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

    <!-- Inclua o Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <style>
        .botton-content form button{
            border: none;
            background: none;
            /* Add this line if you want to remove the button background */
            cursor: pointer;
            outline: none;
        }
        @media (min-width: 1366px) {
        .ajuste-container {
            margin-left: 65% !important;
            max-width: 75vw !important;
        }
        #sidebar{
            width: 180px;
        }
        #breadcrumb{
            margin-left: 12%;
        }
    }
    </style>

</head>

<body>
    <!--top-Header-menu-->
    <div class="navebarn">
        <div id="user-nav" class="navbar navbar-inverse">
            <ul class="nav">
                <!-- Relatórios -->
                <li class="dropdown">
                    <a href="#" class="tip-right dropdown-toggle" data-toggle="dropdown" title="Relatórios">
                        <i class='bx bx-pie-chart-alt-2 iconN'></i><span class="text"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/relatorios/os">Ordens de Serviço</a></li>
                        <li><a href="/relatorios/maquinas">Maquinas</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <!-- New User -->
    <div id="userr" style="margin-top: -15px;padding-right: 45px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">
        <div class="user-names userT0">
            <?php
            function saudacao()
            {
                $hora = date('H');
                if ($hora >= 8 && $hora < 12) {
                    return 'Bom dia ';
                } elseif ($hora >= 12 && $hora < 18) {
                    return 'Boa tarde ';
                } else {
                    return 'Boa noite ';
                }
            }

            $login = '';
            echo saudacao($login); // Irá retornar conforme o horário
            ?>
        </div>
        <!-- <div class="userT">admin</div> -->

        <section style="display:block;position:absolute;right:10px">
            <div class="profile">
                <div class="profile-img">
                    <a href="/minhaConta">
                        <img src="{{ asset('images/User.png') }}" alt="">
                    </a>
                </div>
            </div>
        </section>
    </div>
    <!-- End User -->


    <!--start-top-serch-->
    <div style="display: none" id="search">
        <form action="mapos/pesquisar">
            <input type="text" name="termo" placeholder="Pesquisar..." />
            <button type="submit" class="tip-bottom" title="Pesquisar"><i class="fas fa-search fa-white"></i></button>
        </form>
    </div>
    <!--close-top-serch-->








    <!--sidebar-menu-->
    <nav id="sidebar" class="menu-closed">

        <a href="#" class="visible-phone">
            <div class="mode">
                <div class="moon-menu">
                    <i class='bx bx-chevron-right iconX open-2'></i>
                    <i class='bx bx-chevron-left iconX close-2'></i>
                </div>
            </div>
        </a>
        <!-- Start Pesquisar-->
        <!-- <li class="search-box">
            <form style="display: flex" action="pesquisar">
                <button style="background:transparent;border:transparent" type="submit" class="tip-bottom" title="">
                    <i class='bx bx-search iconX'></i></button>
                <input style="background:transparent; color:#313030;border:transparent" type="search" name="termo" placeholder="Pesquise aqui...">
                <span class="title-tooltip">Pesquisar</span>
            </form>
        </li> -->
        <!-- End Pesquisar-->

        <div class="menu-bar" style="display:flex; justify-content: space-around;">
            <div class="menu">

                <ul class="menu-links" style="position: relative;">
                    <li class="<?php if (isset($menuPainel)) {
                                    echo 'active';
                                }; ?>">
                        <a class="tip-bottom" title="" href="/dashboard"><i class='bx bx-home-alt iconX'></i>
                            <span class="title nav-title">Início</span>
                            <span class="title-tooltip">Início</span>
                        </a>
                    </li>

                    <li class="<?php if (isset($menuClientes)) {
                                    echo 'active';
                                }; ?>">
                        <a class="tip-bottom" title="" href="/clientes">
                            <i class='bx bx-user iconX'></i>
                            <span class="title">Módulos</span>
                            <span class="title-tooltip">Módulos</span>
                        </a>
                    </li>

                    <li class="<?php if (isset($menuProdutos)) {
                                    echo 'active';
                                } ?>">
                        <a class="tip-bottom" title="Produtos" href="/produtos">
                            <i class="bx bx-basket iconX"></i>
                            <span class="title">Vendas</span>
                            <span class="title-tooltip">Vendas</span>
                        </a>
                    </li>


                    <li class="<?php if (isset($menuServicos)) {
                                    echo 'active';
                                } ?>">
                        <a class="tip-bottom" title="Lojas" href="/lojas">
                            <i class='bx bx-wrench iconX'></i>
                            <span class="title">Lojas</span>
                            <span class="title-tooltip">Lojas</span>
                        </a>

                    </li>


                    <!-- <li class="<?php if (isset($menuVendas)) {
                                        echo 'active';
                                    } ?>">
                        <a class="tip-bottom" title="Ordens" href="ordens">
                            <i class='bx bx-list-alt iconX'></i>
                            <span class="title">Ordens</span>
                            <span class="title-tooltip">Ordens</span>
                        </a>

                    </li> -->


                    <li class="<?php if (isset($menuOs)) {
                                    echo 'active';
                                } ?>">
                        <a class="tip-bottom" title="Ordens de Serviço" href="/os">
                            <i class='bx bx-file iconX'></i>
                            <span class="title">Extratos</span>
                            <span class="title-tooltip">Extratos</span>
                        </a>

                    </li>


                    <li class="<?php if (isset($menuGarantia)) {
                                    echo 'active';
                                } ?>">
                        <a class="tip-bottom" title="Garantias" href="/garantias">
                            <i class='bx bx-receipt iconX'></i>
                            <span class="title">Cupons</span>
                            <span class="title-tooltip">Cupons</span>
                        </a>


                    </li>


                    <li class="<?php if (isset($menuArquivos)) {
                                    echo 'active';
                                } ?>">
                        <a class="tip-bottom" title="Máquinas" href="/maquinas">
                            <i class="bx bx-cart iconX"></i>
                            <span class="title">Controle Manual</span>
                            <span class="title-tooltip">Controle Manual</span>
                        </a>

                    </li>

                    <li class="<?php if (isset($menuArquivos)) {
                                    echo 'active';
                                } ?>">
                        <a class="tip-bottom" title="Máquinas Explodidas" href="/maquinas-explodidas">
                            <i class="bx bxs-file iconX"></i>
                            <span class="title"></span>
                            <span class="title-tooltip"></span>
                        </a>

                    </li>

                    <li class="<?php if (isset($menuCobrancas)) {
                                    echo 'active';
                                }; ?>">
                        <a class="tip-bottom" title="" href="/usuarios"><i class='bx bx-user iconX'></i>
                            <span class="title">Usuários</span>
                            <span class="title-tooltip">Usuários</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="botton-content">
                <form method="post" action="/logout">
                    @csrf
                    <li>
                        <button type="submit" class="tip-bottom" title="" style="display: flex; justify-content:center;">
                            <i class='bx bx-log-out-circle iconX' style="padding-top: 2px;"></i>
                            <span class="title">Sair</span>
                            <span class="title-tooltip">Sair</span>
                        </button>
                    </li>
                </form>
            </div>

        </div>
    </nav>
    <!--End sidebar-menu-->

    
    <div id="content">
        <!--start-top-serch-->
        <div id="content-header">
            <div></div>
            <div id="breadcrumb">
                <a href="/dashboard" title="" class="tip-bottom">Início</a>

                @if(request()->segment(1) !== null)
                <a href="{{ '/' . request()->segment(1) }}" class="tip-bottom" title="{{ ucfirst(request()->segment(1)) }}">
                    {{ ucfirst(request()->segment(1)) }}
                </a>

                @if(request()->segment(2) !== null)
                <a href="{{ '/' . request()->segment(1) . '/' . request()->segment(2) . '/' . request()->segment(3) }}" class="current tip-bottom" title="{{ ucfirst(request()->segment(2)) }}">
                    {{ ucfirst(request()->segment(2)) }}
                </a>
                @endif
                @endif
            </div>

        </div>
        <div class="container-flu">
            <div class="row-fluid">

                <div class="span12">
                    @if($var = session()->get('success'))
                    <script>
                        swal("Sucesso!", "<?php echo str_replace('"', '', $var); ?>", "success");
                    </script>
                    @endif

                    @if($var = session()->get('error'))
                    <script>
                        swal("Falha!", "<?php echo str_replace('"', '', $var); ?>", "error");
                    </script>
                    @endif

                    @isset($view)
                    {{ view($view)->render() }}
                    @endisset
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <div class="row-fluid">
        <div id="footer" class="span12">
            <a class="pecolor" href="https://github.com/SilvanMoura" target="_blank">
                <?= date('Y'); ?> &copy; Silvan Moura - Café da Fazenda - Versão: 1.0
            </a>
        </div>
    </div>
    <!--end-Footer-part-->
    <script src="{{ asset('js/matrix.js') }}"></script>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var dataTableEnabled = '1';
        if (dataTableEnabled == '1') {
            $('#tabela').dataTable({
                "ordering": false,
                "language": {
                    "url": "{{ asset('js/dataTable_pt-br.json') }}"
                }
            });
        }
    });
</script>

</html>
