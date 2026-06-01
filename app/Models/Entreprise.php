<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'siret', 'secteur', 'adresse', 'ville', 'pays',
        'site_web', 'logo_url', 'description', 'telephone',
        'email_contact', 'taille', 'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function employeurs()
    {
        return $this->hasMany(Employeur::class);
    }

    public function offres()
    {
        return $this->hasManyThrough(Offre::class, Employeur::class);
    }

    public function scopeVerifiee($query)
    {
        return $query->where('is_verified', true);
    }
}
