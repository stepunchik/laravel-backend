<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Conversation;

class ConversationChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, $conversationId): array|bool
    {
        $conversation = Conversation::find($conversationId);

        if (!$conversation) return false;

        return $conversation->first_user === $user->id || $conversation->second_user === $user->id;
    }
}
