<?php

namespace App\Models;

use App\Enums\PartenaireStatut;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Partenaire extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'raison_sociale',
        'adresse',
        'cp',
        'ville',
        'nbrs_salaries',
        'secteur_activite',
        'telephone_1',
        'telephone_2',
        'ca',
        'siret',
        'statut',
        'vtiger_id'
    ];

    protected $casts = [
        'ca'           => 'decimal:2',
        'nbrs_salaries' => 'integer',
        'statut'        => PartenaireStatut::class,
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_partenaire');
    }
}
