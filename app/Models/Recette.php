<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/* Par default quand on crée un nouveau model, 
la table qui lui est associée c'est le nom de la class en minuscule avec un 's' à la fin */

class Recette extends Model
{
    
    protected $fillable = [
        'cuisinier_id', 'title', 'slug', 'description', 'difficulty', 'temps', 'cout', 'nb_pax', 'img', 'ingredients', 'etapes',
    ];

    public function cuisinier(): BelongsTo
    {
        return $this->belongsTo(Cuisinier::class);
    }

    
}