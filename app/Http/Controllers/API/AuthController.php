<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\SignupRequest;
use App\Http\Requests\LoginRequest;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{	
    public function signup(SignupRequest $request)
    {		
		$user = User::create($request->validated());
		
		$user->assignRole('user');

		$token = $user->createToken('main')->plainTextToken;

        return response(compact('user', 'token'));
    }
	
	public function login(LoginRequest $request) 
	{
		$credentials = $request->validated();
		
		if(!Auth::attempt($credentials)) {
			return response([
                'message' => 'Неверные данные для входа.'
            ], 422);
		}
		
		$user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user', 'token'));
	}
	
	public function logout(Request $request) 
	{
		$user = $request->user();
		$user->currentAccessToken()->delete();
		Auth::logout();
		return response('', 204);
	}
}