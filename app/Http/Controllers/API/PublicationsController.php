<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Publication;
use App\Models\User;
use App\Models\Grade;
use App\Http\Requests\PublicationRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class PublicationsController extends Controller {	
    public function guestFeed() {	
		$publications = Publication::all();

        return response()->json(['publications' => $publications]);
    }

    public function index() {	
		$userId = Auth::id();

		$publications = Publication::all();

		$gradedPublications = $userId
			? Grade::where('user_id', $userId)->get()
			: [];

        return response()->json(['publications' => $publications, 'gradedPublications' => $gradedPublications]);
    }

    public function getUserPublications(User $user) {
		return response()->json(['publications' => $user->publications]);
    }

    public function store(PublicationRequest $request) {
        $validatedData = $request->validated();
		
		$path = $request->file('image')->store('publications', 'public');
		
		Publication::create([
			'title' => $validatedData['title'],
            'text' => $validatedData['text'],
			'image' => $path,
		]);
		
		return; 
    }
	
	public function update(PublicationRequest $request, Publication $publication) {
		$validatedData = $request->validated();
		
		if ($request->hasFile('image')) {
			if ($publication->image) {
				Storage::delete('public/' . $publication['image']);
			}

			$imagePath = $request->file('image')->store('publications', 'public');
			$validatedData['image'] = $imagePath;
		} else {
			$validatedData['image'] = $publication->image;
		}

		$publication->update($validatedData);
		
		return; 
	}
	
	public function destroy(Publication $publication) {		
		Storage::delete('public/' . $publication['image']);

		$publication->delete();
		
		return;
	}

}