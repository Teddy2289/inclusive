<?php

namespace App\Repositories\Contracts;

use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface
{
    public function paginate(?int $partenaire_id, int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): Contact;
    public function create(array $data): Contact;
    public function update(Contact $contact, array $data): Contact;
    public function delete(Contact $contact): bool;
}
