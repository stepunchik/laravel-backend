<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use HasFactory;

    protected $table = 'publications';

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'image',
        'moderation_state',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
