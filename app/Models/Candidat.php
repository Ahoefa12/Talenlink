<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'telephone', 'ville', 'pays', 'bio',
        'date_naissance', 'niveau_etude', 'domaine_etude', 'etablissement',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cvs()
    {
        return $this->hasMany(CV::class);
    }

    public function cvPrincipal()
    {
        return $this->hasOne(CV::class)->where('is_principal', true);
    }

    public function competences()
    {
        return $this->belongsToMany(Competence::class, 'candidat_competence')
                    ->withPivot('niveau')
                    ->withTimestamps();
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function offresEnregistrees()
    {
        return $this->belongsToMany(Offre::class, 'saved_offers')->withTimestamps();
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }


    public function getNomCompletAttribute(): string
    {
        return $this->user->nom;
    }
}
