<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartenaireRequest;
use App\Http\Resources\PartenaireResource;
use App\Services\PartenaireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class PartenaireController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private PartenaireService $service) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Partenaire::class);
        $data = $this->service->list(request('per_page', 15));
        return PartenaireResource::collection($data)->response();
    }

    public function store(StorePartenaireRequest $request): JsonResponse
    {
        $this->authorize('create', \App\Models\Partenaire::class);
        $partenaire = $this->service->create($request->validated());
        return (new PartenaireResource($partenaire))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        $partenaire = $this->service->findById($id);
        $this->authorize('view', $partenaire);
        return (new PartenaireResource($partenaire))->response();
    }

    public function update(StorePartenaireRequest $request, int $id): JsonResponse
    {
        $partenaire = $this->service->update($id, $request->validated());
        return (new PartenaireResource($partenaire))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $partenaire = $this->service->findById($id);

        if ($partenaire->vtiger_id) {
            try {
                $success = app(\App\Services\VTigerService::class)
                    ->deactivateLead($partenaire->vtiger_id);

                Log::info('Désactivation vTiger', [
                    'partenaire_id' => $id,
                    'vtiger_id'     => $partenaire->vtiger_id,
                    'success'       => $success,
                ]);
            } catch (\Exception $e) {
                Log::warning('vTiger deactivate failed: ' . $e->getMessage());
            }
        } else {
            Log::info('Pas de vtiger_id pour le partenaire ' . $id);
        }

        $this->service->delete($id);
        return response()->json(['message' => 'Supprimé avec succès'], 200);
    }
}
