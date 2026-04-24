<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Partenaire;

interface PartenaireRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Partenaire;
    public function create(array $data): Partenaire;
    public function update(Partenaire $partenaire, array $data): Partenaire;
    public function delete(Partenaire $partenaire): bool;
}
