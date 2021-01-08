<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $req)
    {
        $lessons = Lesson::query();
        
        $chapterId = $req->chapter_id;
        $lessons->when($chapterId, function($query) use ($chapterId) {
            return $query->where('chapter_id', $chapterId);
        });

        return response()->json([
            'status' => 'success',
            'data' => $lessons->get()
        ]);
    }

    public function show($id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function store(Request $req)
    {
        $rules = [
            'name' => ['required', 'string'],
            'video' => ['required', 'string'],
            'chapter_id' => ['required', 'integer', 'exists:chapters,id']
        ];

        $data = $req->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function update(Request $req, $id)
    {
        $rules = [
            'name' => 'string',
            'video' => 'string',
            'chapter_id' => ['integer', 'exists:chapters,id']
        ];

        $data = $req->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $lesson->fill($data);
        $lesson->save();
        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function destroy($id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $lesson->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'lesson deleted'
        ]);
    }
}
