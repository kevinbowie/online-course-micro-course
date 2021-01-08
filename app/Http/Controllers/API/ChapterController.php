<?php

namespace App\Http\Controllers\API;

use App\Chapter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function index(Request $req)
    {
        $chapters = Chapter::query();
        $courseId = $req->course_id;
        $chapters->when($courseId, function($query) use ($courseId) {
            return $query->where('course_id', $courseId);
        });

        return response()->json([
            'status' => 'success',
            'data' => $chapters->get()
        ]);
    }

    public function show($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function store(Request $req)
    {
        $rules = [
            'name' => ['required', 'string'],
            'course_id' => ['required', 'integer', 'exists:courses,id']
        ];

        $data = $req->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $chapter = Chapter::create($data);
        return response()->json([
            'status' => 'success',
            'message' => $chapter
        ]);
    }

    public function update(Request $req, $id)
    {
        $rules = [
            'name' => 'string',
            'course_id' => ['integer', 'exists:courses,id']
        ];

        $data = $req->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $chapter->fill($data);
        $chapter->save();
        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function destroy($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $chapter->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'chapter deleted'
        ]);  
    }
}
