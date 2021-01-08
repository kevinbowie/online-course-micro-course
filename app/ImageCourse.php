<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageCourse extends Model
{
    protected $table = 'image_courses';
    protected $fillable = ['course_id', 'image'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
