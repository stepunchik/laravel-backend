<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Publication;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FeedController extends Controller
{	
    public function index()
    {		
		$publications = Publication::all();

        return response(compact('publications'));
    }
}