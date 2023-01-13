<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'body', 'recette_id',
    ];

    public function cuisinier(): BelongsTo
    {
        return $this->belongsTo(Cuisinier::class);
    }

}