<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function store(Request $req)
    {
        $rules = [
            'image' => ['required', 'url'],
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

        $imageCourse = ImageCourse::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $imageCourse
        ]); 
    }

    public function destroy($id)
    {
        $imageCourse = ImageCourse::find($id);
        if (!$imageCourse) {
            return response()->json([
                'status' => 'error', 
                'message' => 'image course not found'
            ], 404);
        }

        $imageCourse->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'image course deleted'
        ]);
    }
}
