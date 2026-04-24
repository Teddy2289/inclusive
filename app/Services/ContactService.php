<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository,
        private readonly StatutService $statutService,
    ) {}

    public function list(int $partenaire_id = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($partenaire_id, $perPage);
    }

    public function findById(int $id): Contact
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Contact
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Contact
    {
        $contact = $this->repository->findById($id);

        return $this->repository->update($contact, $data);
    }

    public function delete(int $id): bool
    {
        $contact = $this->repository->findById($id);

        return $this->repository->delete($contact);
    }

    public function changerStatut(int $id, string $nouveauStatut): Contact
    {
        $contact = $this->repository->findById($id);

        return $this->statutService->changerStatutContact($contact, $nouveauStatut);
    }

    public function transitionsDisponibles(int $id): array
    {
        $contact = $this->repository->findById($id);

        return $this->statutService->transitionsContact($contact);
    }
}
