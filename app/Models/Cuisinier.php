<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* Par default quand on crée un nouveau model,
la table qui lui est associée c'est le nom de la class en minuscule avec un 's' à la fin */

class Cuisinier extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'password', 'img',
    ];

    public function recettes(): HasMany
    {
        return $this->hasMany(Recette::class)->orderBy('id', 'desc');
    }
}
