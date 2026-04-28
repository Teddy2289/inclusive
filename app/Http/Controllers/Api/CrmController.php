<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VTigerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CrmController extends Controller
{
    public function __construct(private VTigerService $vtiger) {}

    public function accounts(Request $request): JsonResponse
    {
        try {
            $page   = $request->integer('page', 1);
            $limit  = $request->integer('per_page', 20);
            $search = $request->string('search', '');

            $accounts = $this->vtiger->getAccounts($page, $limit, $search);
            $total    = $this->vtiger->countAccounts($search);

            return response()->json([
                'data' => $accounts,
                'meta' => [
                    'current_page' => $page,
                    'per_page'     => $limit,
                    'total'        => $total,
                    'last_page'    => ceil($total / $limit),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('CRM getAccounts error: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur CRM'], 500);
        }
    }
}
