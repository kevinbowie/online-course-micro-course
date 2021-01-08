<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'mentors' => 'API\MentorController',
    'courses' => 'API\CourseController',
    'chapters' => 'API\ChapterController',
    'lessons' => 'API\LessonController',
    'image-courses' => 'API\ImageCourseController',
    'my-courses' => 'API\MyCourseController',
    'reviews' => 'API\ReviewController'
]);
Route::post('my-courses/premium', 'API\MyCourseController@createPremiumAccess');