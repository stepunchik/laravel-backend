<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ConversatonRequest;

use App\Models\Conversation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConversationsController extends Controller
{
    public function index() {
        $userId = Auth::id();

        $conversations = Conversations::where('first_user', $userId)->get();

        return response()->json($conversations);
    }

    public function show(Conversation $conversation) {
        return response()->json($conversation->messages);
    }

    public function store(ConversationRequest $request) {
        $validatedData = $request->validated();

        $userId = Auth::id();
		
		Conversation::create([
			'name' => $validatedData['name'],
            'first_user' => $userId,
			'second_user' => $validatedData['second_user'],
		]);
		
		return; 
    }

	public function destroy(Conversation $conversation) {		
		$conversation->delete();
		
        return response()->json(['message' => 'Диалог удален']);
	}
}
