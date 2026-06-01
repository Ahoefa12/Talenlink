<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employeur extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'entreprise_id', 'poste'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function offres()
    {
        return $this->hasMany(Offre::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Publier une offre
     */
    public function publierOffre(array $details): Offre
    {
        return $this->offres()->create(array_merge($details, [
            'statut' => 'publiee',
        ]));
    }

    /**
     * Fermer une offre
     */
    public function fermerOffre(int $offreId): bool
    {
        return (bool) $this->offres()->where('id', $offreId)->update(['statut' => 'fermee']);
    }
}
