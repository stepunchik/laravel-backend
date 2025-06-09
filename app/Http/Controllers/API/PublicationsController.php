<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicationEditRequest;
use App\Http\Requests\PublicationRequest;
use App\Models\Grade;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PublicationsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $publications = Publication::all();

        $gradedPublications = $userId
            ? Grade::where('user_id', $userId)->get()
            : [];

        return response()->json(['publications' => $publications, 'gradedPublications' => $gradedPublications]);
    }

    public function show(Publication $publication)
    {
        return response()->json(['publication' => $publication]);
    }

    public function getUserPublications(User $user)
    {
        $publications = $user->publications;

        $gradedPublications = Grade::where('user_id', Auth::id())->get();

        return response()->json([
            'publications' => $publications,
            'gradedPublications' => $gradedPublications,
        ]);
    }

    public function store(PublicationRequest $request)
    {
        $validatedData = $request->validated();

        $userId = Auth::id();

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('publications', 'public');
            $imageUrl = URL::to('/').Storage::url($path);
        }

        $publication = Publication::create([
            'title' => $validatedData['title'],
            'text' => $validatedData['text'],
            'image' => $imageUrl,
            'user_id' => $userId,
        ]);

        return response()->json(['publication' => $publication]);
    }

    public function update(PublicationEditRequest $request, Publication $publication)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            if ($publication->image) {
                $deletingPath = substr($publication['image'], 29, strlen($publication['image']));
                Storage::delete('public/'.$deletingPath);
            }

            $path = $request->file('image')->store('publications', 'public');
            $imageUrl = URL::to('/').Storage::url($path);
            $validatedData['image'] = $imageUrl;
        } else {
            $validatedData['image'] = $publication->image;
        }

        $publication->update($validatedData);

        return response()->json(['publication' => $publication]);
    }

    public function destroy(Publication $publication)
    {
        $deletingPath = substr($publication['image'], 29, strlen($publication['image']));
        Storage::delete('public/'.$deletingPath);

        $publication->delete();

    }
}
