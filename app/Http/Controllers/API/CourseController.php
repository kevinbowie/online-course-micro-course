<?php

namespace App\Http\Controllers\API;

use App\Chapter;
use App\Course;
use App\Http\Controllers\Controller;
use App\MyCourse;
use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $req)
    {
        $courses = Course::query();

        $q = $req->query('q');
        $status = $req->query('status');

        $courses->when($q, function($query) use ($q) {
            return $query->whereRaw("name LIKE '%" . strtolower($q) . "%'");
        });

        $courses->when($status, function($query) use ($status) {
            return $query->where('status', $status);
        });

        return response()->json([
            'status' => 'success',
            'data' => $courses->paginate(10)
        ]);
    }

    public function show($id)
    {
        $course = Course::with(['images', 'reviews', 'chapters.lessons', 'mentor'])
                    ->find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $course['total_student'] = MyCourse::where('course_id', $id)->count();

        $totalVideos = Chapter::where('course_id', $id)
                                    ->withCount('lessons')
                                    ->get()
                                    ->toArray();
        $course['total_video'] = array_sum(
            array_column($totalVideos, 'lessons_count')
        );

        if (count($course['reviews']) > 0) {
            $reviews = $course['reviews']->toArray();
            $userIds = array_column($reviews, 'user_id');
            $users = getUserByIds($userIds);
            if (!isset($users['status']) || $users['status'] === 'error') {
                $reviews = [];
            } else {
                foreach($reviews as $key => $review) {
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    $reviews[$key]['users'] = $users['data'][$userIndex];
                }
            }        
            $course['reviews'] = $reviews;
        }

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function store(Request $req)
    {
        $rules = [
            'name' => ['required', 'string'],
            'certificate' => ['required', 'boolean'],
            'thumbnail' => ['string', 'url'],
            'type' => ['required', 'in:free,premium'],
            'status' => ['required', 'in:draft,published'],
            'price' => 'integer',
            'level' => ['required', 'in:all-level,beginner,intermediate,advance'],
            'mentor_id' => ['required', 'integer', 'exists:mentors,id'],
            'description' => 'string'
        ];

        $data = $req->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $course = Course::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function update(Request $req, $id)
    {
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => ['string', 'url'],
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advance',
            'mentor_id' => ['integer', 'exists:mentors,id'],
            'description' => 'string'
        ];

        $data = $req->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $course->fill($data);
        $course->save();
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $course->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'course deleted'
        ]);
    }
}
