<?php

namespace App\Models;

use App\Enums\ContactStatut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partenaire_id',
        'conseiller_nom',
        'conseiller_prenom',
        'statut',
        'date_premier_contact',
        'commentaires',
        'poste',
        'tel',
        'vtiger_id',
    ];

    protected $casts = [
        'date_premier_contact' => 'date',
        'date_rdv' => 'date',
        'heure_rdv' => 'datetime:H:i',
        'statut' => ContactStatut::class,
    ];

    public function getConseillerFullNameAttribute(): ?string
    {
        if ($this->conseiller_nom || $this->conseiller_prenom) {
            return trim("{$this->conseiller_prenom} {$this->conseiller_nom}");
        }
        return null;
    }

 public function partenaires(): BelongsToMany
{
    return $this->belongsToMany(Partenaire::class, 'contact_partenaire');
}
}
