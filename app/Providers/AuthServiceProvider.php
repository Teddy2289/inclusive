<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\Partenaire;
use App\Policies\ContactPolicy;
use App\Policies\PartenairePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Partenaire::class => PartenairePolicy::class,
        Contact::class    => ContactPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
