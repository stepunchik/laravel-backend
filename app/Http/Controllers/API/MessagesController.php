<?php

namespace App\Http\Controllers\API;

use App\Events\MessageDeletedEvent;
use App\Events\MessageSentEvent;
use App\Events\MessageUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageEditRequest;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function store(MessageRequest $request)
    {
        logger($request->all());
        $validatedData = $request->validated();

        $userId = Auth::id();
        $user = Auth::user();

        $message = Message::create([
            'sender_id' => $userId,
            'conversation_id' => (int) $validatedData['conversation_id'],
            'text' => $validatedData['text'],
            'is_read' => false,
            'created_at' => now(),
        ]);

        broadcast(new MessageSentEvent($message, $userId))->toOthers();

        return response()->json(['message' => $message]);
    }

    public function update(MessageEditRequest $request, Message $message)
    {
        $validatedData = $request->validated();

        $message->update($validatedData);

        broadcast(new MessageUpdatedEvent($message))->toOthers();

        return response()->json(['message' => $message]);
    }

    public function destroy(Message $message)
    {
        $message->delete();

        broadcast(new MessageDeletedEvent($message->id, $message->conversation_id))->toOthers();

        return response()->json(['message' => 'Сообщение удалено']);
    }
}
