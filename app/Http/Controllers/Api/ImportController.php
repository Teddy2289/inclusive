<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\PartenairesImport;
use App\Models\Import;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file             = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        // Créer la trace AVANT l'import
        $importRecord = Import::create([
            'filename'          => $originalFilename,
            'original_filename' => $originalFilename,
            'status'            => 'pending',
            'user_id'           => auth()->id(),
        ]);

        try {
            Excel::import(
                new PartenairesImport($importRecord),
                $file
            );

            return response()->json([
                'message'  => 'Import réalisé avec succès',
                'import'   => $importRecord->fresh(), // retourne les stats à jour
            ], 200);
        } catch (\Exception $e) {
            $importRecord->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erreur lors de l\'import : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function history(): JsonResponse
    {
        $imports = Import::with('user')
            ->latest()
            ->paginate(20);

        return response()->json($imports);
    }
}
