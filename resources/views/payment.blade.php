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

    @media (min-width: 1368px) {
        .ajuste-container {
            margin-left: 7% !important;
            max-width: 93% !important;
            max-height: 85vh !important;
            margin-top: -46%
        }
    }

    @media (max-width: 1366px) {
        .ajuste-container {
            margin-left: 2% !important;
            max-width: 93% !important;
            max-height: 85vh !important;
            margin-top: 5%;
        }
    }
</style>
<div class="ajuste-container" style="height: 100vh; width: 99vw; margin-bottom:5%;">
    <div class="new122" style="margin: 1% 1% 0 7%;">
        <ul class="nav nav-tabs">
            <li><a data-toggle="tab" href="#tab1">Consumidor</a></li>
        </ul>
    </div>
    <div class="widget-content tab-content new122" style="margin: 1% 1% 0 7%;">
        <div id="tab1" class="tab-pane active">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Módulo</th>
                        <th>Valor</th>
                        <th>Nome Comprador</th>
                        <th>Documento</th>
                        <th>Nº Pagamento</th>
                        <th>Nome Loja</th>
                        <th>Ponto de Venda</th>
                        <th>Data da Venda</th>
                    </tr>
                </thead>
                <tbody>
                    @if($paymentData->count() > 0)
                    @foreach ($paymentData as $r)
                    <tr>
                        <td style="width:5%">{{ $r->id }}</td>
                        <td style="width:5%">{{ $r->modulo }}</td>
                        <td style="width:5%">{{ $r->valor }}</td>
                        <td style="width:15%">{{ $r->receipt->nome_remetente }}</td>
                        <td style="width:10%">{{ $r->receipt->cpf_remetente }}</td>
                        <td style="width:10%">{{ $r->receipt->id_mercado_pago }}</td>
                        <td style="width:15%">{{ $r->store_name }}</td>
                        <td style="width:15%">{{ $r->pos_name }}</td>
                        <td style="width:15%">{{ $r->created_at }}</td>

                        <!--@if($r->status !== 'Estornado')
                        <td>
                            <a href="{{ '/pagamento/estorno/'. $r->id_payment }}" style="background-color:red;" class="btn tip-top" title="Reembolso">
                                <i class="bx bx-wallet"></i>
                            </a>
                        </td>
                        @endif-->
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
@endsection