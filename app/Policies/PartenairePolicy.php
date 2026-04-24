<?php

namespace App\Policies;

use App\Models\Partenaire;
use App\Models\User;

class PartenairePolicy
{
    /**
     * Admin peut tout faire sans vérification supplémentaire
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null; // laisse passer aux méthodes suivantes
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('partenaires.view');
    }

    public function view(User $user, Partenaire $partenaire): bool
    {
        return $user->hasPermissionTo('partenaires.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('partenaires.create');
    }

    public function update(User $user, Partenaire $partenaire): bool
    {
        return $user->hasPermissionTo('partenaires.edit');
    }

    public function delete(User $user, Partenaire $partenaire): bool
    {
        return $user->hasPermissionTo('partenaires.delete');
    }

    public function import(User $user): bool
    {
        return $user->hasPermissionTo('partenaires.import');
    }
}
