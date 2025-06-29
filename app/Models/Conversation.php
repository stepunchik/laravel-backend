<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'name',
        'first_user',
        'second_user',
    ];

    public function firstUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_user');
    }

    public function secondUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
