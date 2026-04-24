<?php

namespace App\Repositories;

use App\Models\Partenaire;
use App\Repositories\Contracts\PartenaireRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PartenaireRepository implements PartenaireRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Partenaire::with('contacts')->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Partenaire
    {
        return Partenaire::with('contacts')->findOrFail($id);
    }

    public function create(array $data): Partenaire
    {
        return Partenaire::create($data);
    }

    public function update(Partenaire $partenaire, array $data): Partenaire
    {
        $partenaire->update($data);
        return $partenaire->fresh();
    }

    public function delete(Partenaire $partenaire): bool
    {
        return $partenaire->delete();
    }
}
