<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\StatutService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    use AuthorizesRequests;
    public function __construct(
        private readonly StatutService $statutService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Contact::class);

        $contacts = Contact::with('partenaires')
            ->when(
                $request->partenaire_id,
                fn($q) => $q->whereHas(
                    'partenaires',
                    fn($q2) => $q2->where('partenaires.id', $request->partenaire_id)
                )
            )
            ->paginate($request->get('per_page', 15));

        // ✅ collection() pour les listes paginées, pas new ContactResource()
        return ContactResource::collection($contacts)->response();
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $this->authorize('create', Contact::class);

        $data = $request->validated();
        $partenaireIds = $data['partenaire_ids'];
        unset($data['partenaire_ids']);

        $contact = Contact::create($data);
        $contact->partenaires()->sync($partenaireIds);

        return (new ContactResource($contact->load('partenaires')))
            ->response()->setStatusCode(201);
    }
    public function show(Contact $contact): JsonResponse
    {
        $this->authorize('view', $contact);

        return (new ContactResource($contact->load('partenaires')))->response();
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $this->authorize('update', $contact);

        $data = $request->validated();
        $partenaireIds = $data['partenaire_ids'] ?? null;
        unset($data['partenaire_ids']);

        $contact->update($data);

        if ($partenaireIds !== null) {
            $contact->partenaires()->sync($partenaireIds);
        }

        return (new ContactResource($contact->fresh('partenaires')))->response();
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return response()->json(['message' => 'Contact supprimé avec succès.']);
    }

    public function changerStatut(Request $request, Contact $contact): JsonResponse
    {
        $this->authorize('update', $contact);

        $request->validate(['statut' => 'required|string']);

        $contact = $this->statutService->changerStatutContact($contact, $request->statut);

        return (new ContactResource($contact))->response();
    }

    public function transitionsDisponibles(Contact $contact): JsonResponse
    {
        $this->authorize('view', $contact);

        return response()->json([
            'statut_actuel' => [
                'value' => $contact->statut->value,
                'label' => $contact->statut->label(),
            ],
            'transitions' => $this->statutService->transitionsContact($contact),
        ]);
    }
}
