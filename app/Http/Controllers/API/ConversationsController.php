<?php

namespace App\Http\Controllers\API;

use App\Events\MessageReadEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConversationRequest;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $conversations = Conversation::with('latestMessage')->with(['firstUser', 'secondUser'])
            ->where(function ($query) use ($user) {
                $query->where('first_user', $user->id)
                    ->orWhere('second_user', $user->id);
            })
            ->get();

        $sorted = $conversations->sortByDesc(function ($conversation) {
            return optional($conversation->latestMessage)->created_at;
        })->values();

        $unreadMessages = $sorted->mapWithKeys(function ($conversation) use ($user) {
            $unreadCount = $conversation->messages
                ->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->count();

            return [$conversation->id => $unreadCount];
        });

        return response()->json(['conversations' => $sorted, 'unread_messages' => $unreadMessages]);
    }

public function show(Conversation $conversation)
{
    $userId = Auth::id();

    // Получаем ID непрочитанных сообщений перед обновлением
    $unreadMessages = $conversation->messages()
        ->where('is_read', false)
        ->where('sender_id', '!=', $userId)
        ->pluck('id')
        ->toArray();

    // Обновляем статус сообщений
    if (!empty($unreadMessages)) {
        $conversation->messages()
            ->whereIn('id', $unreadMessages)
            ->update(['is_read' => true]);
            
        // Отправляем событие с ID сообщений, которые были прочитаны
        broadcast(new MessageReadEvent($conversation->id, $unreadMessages))->toOthers();
    }

    $messages = $conversation->messages()
        ->orderBy('created_at', 'desc')
        ->paginate(50);

    return response()->json(['conversation' => $conversation, 'messages' => $messages]);
}

    public function store(ConversationRequest $request)
    {
        $validatedData = $request->validated();

        $userId = Auth::id();
        $secondUserId = $validatedData['second_user'];

        $existingConversation = Conversation::where(function ($query) use ($userId, $secondUserId) {
            $query->where('first_user', $userId)->where('second_user', $secondUserId);
        })->orWhere(function ($query) use ($userId, $secondUserId) {
            $query->where('first_user', $secondUserId)->where('second_user', $userId);
        })->first();

        if ($existingConversation) {
            return response()->json(['id' => $existingConversation->id]);
        }

        $conversation = Conversation::create([
            'first_user' => $userId,
            'second_user' => $secondUserId,
        ]);

        return response()->json(['id' => $conversation->id]);
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return response()->json(['message' => 'Диалог удален']);
    }
}
