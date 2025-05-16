<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'sender_id',
        'conversation_id',
        'text',
    ];

    public function conversation(): BelongsTo {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
