<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function getTop() {
        $M = 5;

        $averageRatingOfAllPublications = DB::table('grades')
            ->select(DB::raw('AVG(value) as C'))
            ->value('C');

        $users = DB::table('users')
            ->join('publications', 'users.id', '=', 'publications.user_id')
            ->leftJoin('grades', 'publications.id', '=', 'grades.publication_id')
            ->select(
                'users.id',
                'users.name',
                'users.image',
                DB::raw('COUNT(grades.id) as V'),
                DB::raw('SUM(grades.value) as likes'),
                DB::raw('AVG(grades.value) as R')
            )
            ->groupBy('users.id', 'users.name', 'users.image')
            ->get();

        $usersWithRatings = $users->map(function ($user) use ($M, $averageRatingOfAllPublications) {
            $V = $user->V;
            $R = $user->R ?? 0;
            $C = $averageRatingOfAllPublications ?? 0;

            $rating = (($V / ($V + $M)) * $R) + (($M / ($V + $M)) * $C);

            $percent = $V > 0 ? round(($user->likes / $V) * 100) : 0;

            $user->rating = round($rating, 4);
            $user->percent = $percent;
            $user->votes = $V;

            unset($user->R);
            unset($user->V);
            unset($user->likes);

            return $user;
        });

        $sortedUsers = $usersWithRatings->sortByDesc('rating')->values();

        return response()->json(['top' => $sortedUsers]);
    }

    public function getLastWeekTop() {
        $M = 5;
        $weekAgo = now()->subWeek();

        $averageRatingOfLastWeek = DB::table('grades')
            ->where('grades.created_at', '>=', $weekAgo)
            ->select(DB::raw('AVG(value) as C'))
            ->value('C');

        $users = DB::table('users')
            ->join('publications', 'users.id', '=', 'publications.user_id')
            ->leftJoin('grades', function ($join) use ($weekAgo) {
                $join->on('publications.id', '=', 'grades.publication_id')
                    ->where('grades.created_at', '>=', $weekAgo);
            })
            ->select(
                'users.id',
                'users.name',
                'users.image',
                DB::raw('COUNT(grades.id) as V'),
                DB::raw('SUM(grades.value) as likes'),
                DB::raw('AVG(grades.value) as R')
            )
            ->groupBy('users.id', 'users.name', 'users.image')
            ->get();

        $usersWithRatings = $users->map(function ($user) use ($M, $averageRatingOfLastWeek) {
            $V = $user->V;
            $R = $user->R ?? 0;
            $C = $averageRatingOfLastWeek ?? 0;

            $rating = (($V / ($V + $M)) * $R) + (($M / ($V + $M)) * $C);

            $percent = $V > 0 ? round(($user->likes / $V) * 100) : 0;

            $user->rating = round($rating, 4);
            $user->percent = $percent;
            $user->votes = $V;

            unset($user->R);
            unset($user->V);
            unset($user->likes);

            return $user;
        });

        $sortedUsers = $usersWithRatings->sortByDesc('rating')->values();

        return response()->json(['last_week_top' => $sortedUsers]);
    }

    public function show(User $user) {
        return response()->json($user);
    }
}
