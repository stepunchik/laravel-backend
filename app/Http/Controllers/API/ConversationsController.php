<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ConversationRequest;

use App\Models\Conversation;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConversationsController extends Controller
{
    public function index() {
        $userId = Auth::id();

        $conversations = Conversation::where('first_user', $userId)->get();

        return response()->json(['conversations' => $conversations]);
    }

    public function show(Conversation $conversation) {
        return response()->json(['conversation' => $conversation, 'messages' => $conversation->messages]);
    }

    public function store(ConversationRequest $request) {
        $validatedData = $request->validated();

        $userId = Auth::id();
		
		$conversation = Conversation::create([
			'name' => $validatedData['name'],
            'first_user' => $userId,
			'second_user' => $validatedData['second_user'],
		]);
		
		return response()->json(['id' => $conversation->id]); 
    }

	public function destroy(Conversation $conversation) {		
		$conversation->delete();
		
        return response()->json(['message' => 'Диалог удален']);
	}
}
