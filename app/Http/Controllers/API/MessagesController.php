<?php

namespace App\Http\Controllers\API;

use App\Models\Message;
use App\Events\MessageSentEvent;
use App\Http\Requests\MessageRequest;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store(MessageRequest $request) {
		logger($request->all());   
        $validatedData = $request->validated();

        $userId = Auth::id();
		$user = Auth::user();
		
		$message = Message::create([
			'sender_id' => $userId,
            'conversation_id' => (int)$validatedData['conversation_id'],
			'text' => $validatedData['text'],
		]);

		broadcast(new MessageSentEvent($message))->toOthers();
		
		return response()->json(['message' => $message]); 
    }

	public function destroy(Message $message) {		
		$message->delete();
		
		return response()->json(['message' => 'Сообщение удалено']);
	}
}
