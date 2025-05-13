<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Grade;
use App\Models\Publication;

use App\Http\Requests\GradeRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GradesController extends Controller
{	
    public function like(Publication $publication) {
        return $this->grade($publication, 1);
    }

    public function dislike(Publication $publication) {
        return $this->grade($publication, -1);
    }

    private function grade(Publication $publication, int $value) {
        $userId = Auth::id();

        Grade::create([
            'user_id' => $userId,
            'publication_id' => $publication->id,
            'value' => $value,
        ]);

        return response()->json(['message' => 'Оценка добавлена']);
    }

    public function update(GradeRequest $request, Publication $publication) {
		$validatedData = $request->validated();

        $userId = Auth::id();

        $grade = Grade::where('publication_id', $publication->id)->where('user_id', $userId)->first();

        $grade->update(['value' => $validatedData['value']]);

        return response()->json(['message' => 'Оценка обновлена']);
    }

	public function destroy(Publication $publication) {		
        $userId = Auth::id();

		Grade::where('user_id', $userId)
                        ->where('publication_id', $publication->id)
                        ->delete();
		
        return response()->json(['message' => 'Оценка удалена']);
	}

}