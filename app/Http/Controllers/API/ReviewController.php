<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function store(Request $req)
    {
        $rules = [
            'user_id' => ['required', 'integer'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'note' => 'string'
        ];

        $data = $req->all();
        
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $userId = $req->user_id;
        $courseId = $req->course_id;
        $user = getUser($userId);
        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['http_code']);
        }

        $isExistReview = Review::where([
                            ['course_id', $courseId],
                            ['user_id', $userId]
                        ])->exists();
        if ($isExistReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'review already exist'
            ], 409);
        }

        $review = Review::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $review
        ]);
    }

    public function update(Request $req, $id)
    {
        $rules = [
            'rating' => ['integer', 'min:1', 'max:5'],
            'note' => 'string'
        ];

        $data = $req->except('user_id', 'course_id');

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->fill($data);
        $review->save();
        return response()->json([
            'status' => 'success',
            'data' => $review
        ]);
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'review deleted'
        ]);
    }
}
