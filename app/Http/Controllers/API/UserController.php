<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserEditRequest;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function getTop()
    {
        $topUsers = DB::table('grades')
            ->join('publications', 'grades.publication_id', '=', 'publications.id')
            ->join('users', 'publications.user_id', '=', 'users.id')
            ->where('grades.value', '=', 1)
            ->select('users.id', 'users.name', 'users.image', DB::raw('COUNT(grades.id) as total_likes'))
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total_likes')
            ->limit(5)
            ->get();

        return response()->json(['top' => $topUsers]);
    }

    public function getLastWeekTop()
    {
        $topUsers = DB::table('grades')
            ->join('publications', 'grades.publication_id', '=', 'publications.id')
            ->join('users', 'publications.user_id', '=', 'users.id')
            ->where('grades.created_at', '>=', now()->subWeek())
            ->where('grades.value', '=', 1)
            ->select('users.id', 'users.name', 'users.image', DB::raw('COUNT(grades.id) as total_likes'))
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total_likes')
            ->limit(5)
            ->get();

        return response()->json(['last_week_top' => $topUsers]);
    }

    public function show(User $user)
    {
        $likesQuantity = DB::table('grades')
            ->join('publications', 'grades.publication_id', '=', 'publications.id')
            ->where('publications.user_id', '=', $user->id)
            ->where('grades.value', '=', 1)
            ->count();
        $userPublications = Publication::where('user_id', $user->id)->get();
        $publicationsQuantity = count($userPublications);

        return response()->json([
            'user' => $user,
            'likes_quantity' => $likesQuantity,
            'publications_quantity' => $publicationsQuantity,
        ]);
    }

    public function getCurrentUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'sex' => $user->sex,
            'birthday' => $user->birthday,
            'image' => $user->image,
            'roles' => $user->getRoleNames(),
        ]);
    }

    public function update(UserEditRequest $request, User $user)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            if ($user->image) {
                $deletingPath = substr($user['image'], 29, strlen($user['image']));
                Storage::delete('public/'.$deletingPath);
            }

            $path = $request->file('image')->store('users', 'public');
            $imageUrl = URL::to('/').Storage::url($path);
            $validatedData['image'] = $imageUrl;
        } else {
            $validatedData['image'] = $user->image;
        }

        $user->update($validatedData);

        return response()->json(['user' => $user]);
    }
}
