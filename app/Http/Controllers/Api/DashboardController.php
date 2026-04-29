<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\VTigerService;


class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'partenaires' => [
                'total'        => Partenaire::count(),
                'synced'       => Partenaire::whereNotNull('vtiger_id')->count(),
                'not_synced'   => Partenaire::whereNull('vtiger_id')->count(),
                'deleted'      => Partenaire::onlyTrashed()->count(),
            ],
            'contacts' => [
                'total'  => Contact::count(),
                'synced' => Contact::whereNotNull('vtiger_id')->count(),
            ],
            'jobs' => [
                'pending'  => DB::table('jobs')->count(),
                'failed'   => DB::table('failed_jobs')->count(),
            ],
        ]);
    }

    public function syncStatus(): JsonResponse
    {
        return response()->json([
            'pending'   => DB::table('jobs')->count(),
            'failed'    => DB::table('failed_jobs')->count(),
            'synced'    => Partenaire::whereNotNull('vtiger_id')->count(),
            'total'     => Partenaire::count(),
            'percent'   => Partenaire::count() > 0
                ? round((Partenaire::whereNotNull('vtiger_id')->count() / Partenaire::count()) * 100)
                : 0,
        ]);
    }

    public function clearCrm(VTigerService $vtiger): JsonResponse
    {
        $deleted = $vtiger->deleteAllLeads();
        return response()->json([
            'message' => "$deleted prospects supprimés du CRM",
            'deleted' => $deleted,
        ]);
    }
}
