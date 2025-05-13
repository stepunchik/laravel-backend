<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';

    protected $fillable = [
        'publication_id',
        'user_id',
        'value',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function publication(): BelongsTo {
        return $this->belongsTo(Publication::class);
    }
}
