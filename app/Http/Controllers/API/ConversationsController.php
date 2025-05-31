<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConversationRequest;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::with('latestMessage')
            ->where(function ($query) use ($userId) {
                $query->where('first_user', $userId)
                    ->orWhere('second_user', $userId);
            })
            ->get();

        $sorted = $conversations->sortByDesc(function ($conversation) {
            return optional($conversation->latestMessage)->created_at;
        })->values();

        $unreadMessages = $sorted->mapWithKeys(function ($conversation) use ($userId) {
            $unreadCount = $conversation->messages
                ->where('is_read', false)
                ->where('sender_id', '!=', $userId)
                ->count();

            return [$conversation->id => $unreadCount];
        });

        return response()->json(['conversations' => $sorted, 'unread_messages' => $unreadMessages]);
    }

    public function show(Conversation $conversation)
    {
        $userId = Auth::id();

        $conversation->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->update(['is_read' => true]);

        return response()->json(['conversation' => $conversation, 'messages' => $conversation->messages()->orderBy('created_at')->get()]);
    }

    public function store(ConversationRequest $request)
    {
        $validatedData = $request->validated();

        $userId = Auth::id();
        $secondUserId = $validatedData["second_user"];

        $existingConversation = Conversation::where(function ($query) use ($userId, $secondUserId) {
                $query->where('first_user', $userId)->where('second_user', $secondUserId);
            })->orWhere(function ($query) use ($userId, $secondUserId) {
                $query->where('first_user', $secondUserId)->where('second_user', $userId);
            })->first();

        if ($existingConversation) {
            return response()->json(['id' => $existingConversation->id]);
        }

        $conversation = Conversation::create([
            'name' => $validatedData["name"],
            'first_user' => $userId,
            'second_user' => $secondUserId,
        ]);

        return response()->json(['id' => $conversation->id]);
    }

    public function destroy(Conversation $conversation)
    {
        foreach($conversation->messages as $message) {
            $message->delete();
        }

        $conversation->delete();

        return response()->json(['message' => 'Диалог удален']);
    }
}
