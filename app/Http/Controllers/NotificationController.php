<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Machine;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function handle(Request $request)
    {
        // Captura os dados enviados pelo PagSeguro
        $data = $request->all();

        // Dados da notificação
        $transactionId = $data['transaction_id'] ?? null;
        $referenceId = $data['reference_id'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount']['value'] ?? 0;

        // Verifica se é um pagamento concluído
        if ($status === 'PAID') {
            // Encontra a máquina relacionada ao pagamento
            $machine = Machine::where('reference_id', $referenceId)->first();

            if ($machine) {
                // Adiciona os créditos à máquina
                $machine->credits += $amount / 100; // Conversão de centavos para reais
                $machine->save();

                // Registra a transação
                Transaction::create([
                    'transaction_id' => $transactionId,
                    'reference_id' => $referenceId,
                    'amount' => $amount,
                    'status' => $status,
                ]);
            }
        }

        // Retorna uma resposta HTTP 200 para o PagSeguro
        return response()->json(['message' => 'Notificação processada com sucesso.'], 200);
    }
}
