<?php

namespace App\Http\Controllers;

use App\Services\PagSeguroService;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    private $pagSeguroService;

    public function __construct(PagSeguroService $pagSeguroService)
    {
        $this->pagSeguroService = $pagSeguroService;
    }

    public function generate(Request $request)
    {
        $referenceId = $request->input('machine_id');
        $qrCodeData = $this->pagSeguroService->generateQrCode($referenceId);

        return response()->json($qrCodeData);
    }

    public function qrCodeView()
    {
        return view('qrCodeGenerate');
    }

    public function transactionDetails(Request $request, $transactionId)
    {
        $details = $this->pagSeguroService->getTransactionDetails($transactionId);

        return response()->json($details);
    }
}
