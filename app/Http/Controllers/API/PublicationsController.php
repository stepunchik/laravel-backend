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
use Illuminate\Support\Facades\URL;

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

	public function show(Publication $publication) {
		return response()->json(['publication' => $publication]);
	}

    public function getUserPublications(User $user) {
		$publications = $user->publications;

		$gradedPublications = [];

		if (Auth::check()) {
			$gradedPublications = Grade::where('user_id', Auth::id())->get();
		}

		return response()->json([
			'publications' => $publications,
			'gradedPublications' => $gradedPublications
		]);
    }

    public function store(PublicationRequest $request) {
        $validatedData = $request->validated();

		$userId = Auth::id();

		$imageUrl = null;

		if ($request->hasFile('image')) {
			$path = $request->file('image')->store('publications', 'public');
			$imageUrl = URL::to('/') . Storage::url($path);
		}
		
		Publication::create([
			'title' => $validatedData['title'],
            'text' => $validatedData['text'],
			'image' => $imageUrl,
			'user_id' => $userId
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