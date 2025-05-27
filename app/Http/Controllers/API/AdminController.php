<?php

namespace App\Http\Controllers\API;

use App\Models\Publication;
use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AdminController extends Controller
{
    public function getPublications(Request $request)
    {
$perPage = $request->input('per_page', 10); // Получаем параметр пагинации
    $page = $request->input('page', 1); // Получаем номер страницы
    
    $publications = Publication::paginate($perPage, ['*'], 'page', $page);
    
    return response()->json([
        'data' => $publications->items(),
        'meta' => [
            'total' => $publications->total(),
            'per_page' => $publications->perPage(),
            'current_page' => $publications->currentPage(),
            'last_page' => $publications->lastPage(),
        ]
    ]);
    }

    public function getUsers()
    {
        $users = User::paginate(10);
        return response()->json([
            'data' => $users->items(),
            'meta' => ['total' => $users->total()]
        ]);
    }

    public function approve($id)
    {
        $publication = Publication::findOrFail($id);
        $publication->moderation_state = true;
        $publication->save();

        return response()->json($publication);
    }

    public function reject($id)
    {
        $publication = Publication::findOrFail($id);
        $publication->moderation_state = false;
        $publication->save();

        return response()->json($publication);
    }

	public function destroyPublication(Publication $publication) {		
		$deletingPath = substr($publication['image'], 29, strlen($publication['image']));
		Storage::delete('public/' . $deletingPath);

		$publication->delete();
	}

    
	public function destroyUser(User $user) {		
		$deletingPath = substr($user['image'], 29, strlen($user['image']));
		Storage::delete('public/' . $deletingPath);

		$user->delete();
	}
}
