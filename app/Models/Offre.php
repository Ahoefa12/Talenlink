<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Offre extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employeur_id', 'categorie_id', 'titre', 'description',
        'missions', 'profil_recherche', 'type_offre', 'type_contrat',
        'localisation', 'ville', 'pays', 'teletravail',
        'salaire_min', 'salaire_max', 'devise',
        'niveau_etude', 'experience_min_mois',
        'statut', 'date_expiration', 'nombre_postes', 'vues',
    ];

    protected $casts = [
        'teletravail' => 'boolean',
        'salaire_min' => 'decimal:2',
        'salaire_max' => 'decimal:2',
        'date_expiration' => 'date',
        'vues' => 'integer',
    ];


    public function employeur()
    {
        return $this->belongsTo(Employeur::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function competences()
    {
        return $this->belongsToMany(Competence::class, 'offre_competence')
                    ->withPivot('obligatoire')
                    ->withTimestamps();
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function candidaturesActives()
    {
        return $this->hasMany(Candidature::class)->whereNotIn('statut', ['retiree', 'rejetee']);
    }

    public function sauvegardePar()
    {
        return $this->belongsToMany(Candidat::class, 'saved_offers')->withTimestamps();
    }

    public function signalements()
    {
        return $this->morphMany(Signalement::class, 'cible');
    }

    public function scopePubliee(Builder $query): Builder
    {
        return $query->where('statut', 'publiee')
                     ->where(function ($q) {
                         $q->whereNull('date_expiration')
                           ->orWhere('date_expiration', '>=', now());
                     });
    }

    public function scopeStage(Builder $query): Builder
    {
        
        return $query->where('type_offre', 'stage');
    }

    public function scopeEmploi(Builder $query): Builder
    {
        return $query->where('type_offre', 'emploi');
    }

    public function scopeLocalisation(Builder $query, string $localisation): Builder
    {
        return $query->where('localisation', 'like', "%{$localisation}%");
    }

    public function scopeNiveauEtude(Builder $query, string $niveau): Builder
    {
        return $query->where('niveau_etude', $niveau)->orWhere('niveau_etude', 'indifferent');
    }

    public function scopeRecherche(Builder $query, string $term): Builder
    {
        return $query->whereFullText(['titre', 'description'], $term);
    }


    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    public function estExpiree(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function estOuverte(): bool
    {
        return $this->statut === 'publiee' && !$this->estExpiree();
    }
}
