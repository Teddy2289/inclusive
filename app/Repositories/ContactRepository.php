<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository implements ContactRepositoryInterface
{
    public function paginate(?int $partenaire_id, int $perPage = 15): LengthAwarePaginator
    {
        return Contact::with('partenaire')
            ->when($partenaire_id, fn($q) => $q->where('partenaire_id', $partenaire_id))
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Contact
    {
        return Contact::with('partenaire')->findOrFail($id);
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        $contact->update($data);

        return $contact->fresh();
    }

    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }
}
