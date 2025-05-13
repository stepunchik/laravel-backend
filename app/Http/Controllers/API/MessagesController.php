<?php

namespace App\Http\Controllers\API;

use App\Models\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store(MessageRequest $request) {
        $validatedData = $request->validated();

        $userId = Auth::id();
		
		Message::create([
			'sender_id' => $userId,
            'conversation_id' => $validatedData['conversation_id'],
			'text' => $validatedData['text'],
		]);
		
		return response()->json(['message' => 'Сообщение создано']); 
    }

	public function destroy(Message $message) {		
		$message->delete();
		
		return response()->json(['message' => 'Сообщение удалено']);
	}
}
