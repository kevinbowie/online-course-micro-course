<?php

namespace App\Http\Controllers\API;

use App\Course;
use App\Http\Controllers\Controller;
use App\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{
    public function index(Request $req)
    {
        $myCourses = MyCourse::query()->with('course');

        $userId = $req->user_id;
        $myCourses->when($userId, function($query) use ($userId) {
            return $query->where('user_id', $userId);
        });

        return response()->json([
            'status' => 'success',
            'data' => $myCourses->get()
        ]);
    }

    public function show($id)
    {
        //
    }

    public function store(Request $req)
    {
        $rules = [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'user_id' => ['required', 'integer']
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

        $isExistMyCourse = MyCourse::where([
                ['course_id', $courseId],
                ['user_id', $userId]
            ])->exists();
        if ($isExistMyCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already taken this course'
            ], 409);
        }

        $course = Course::find($courseId);
        if ($course->type === 'premium') {
            if ($course->price === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Price cannot be 0'
                ], 405);
            }

            $order = postOrder([
                'user' => $user['data'],
                'course' => $course->toArray()
            ]);

            if ($order['status'] === 'error') {
                return response()->json([
                    'status' => $order['status'],
                    'message' => $order['message']
                ], $order['http_code']);
            }

            return response()->json([
                'status' => $order['status'],
                'data' => $order['data']
            ]);

        } else {
            $myCourse = MyCourse::create($data);
            return response()->json([
                'status' => 'success',
                'data' => $myCourse
            ]);
        }
    }

    public function update(Request $req, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function createPremiumAccess(Request $req)
    {
        $data = $req->all();
        $myCourse = MyCourse::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $myCourse
        ]);
    }
}
