<?php

namespace App\Services;

use App\Models\Partenaire;
use App\Repositories\Contracts\PartenaireRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PartenaireService
{
    public function __construct(
        private readonly PartenaireRepositoryInterface $repository,
        private readonly StatutService $statutService,
    ) {}

    public function list(int $perPage = 15)
    {
        return Partenaire::query()
            ->when(
                request('search'),
                fn($q, $v) =>
                $q->where('raison_sociale', 'like', "%$v%")
                    ->orWhere('ville', 'like', "%$v%")
            )
            ->when(
                request('ville'),
                fn($q, $v) =>
                $q->where('ville', 'like', "%$v%")
            )
            ->when(
                request('statut'),
                fn($q, $v) =>
                $q->where('statut', $v)
            )
            ->paginate($perPage);
    }

    public function findById(int $id): Partenaire
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Partenaire
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Partenaire
    {
        $partenaire = $this->repository->findById($id);

        return $this->repository->update($partenaire, $data);
    }

    public function delete(int $id): bool
    {
        $partenaire = $this->repository->findById($id);

        return $this->repository->delete($partenaire);
    }

    public function changerStatut(int $id, string $nouveauStatut): Partenaire
    {
        $partenaire = $this->repository->findById($id);

        return $this->statutService->changerStatutPartenaire($partenaire, $nouveauStatut);
    }

    public function transitionsDisponibles(int $id): array
    {
        $partenaire = $this->repository->findById($id);

        return $this->statutService->transitionsPartenaire($partenaire);
    }
}
