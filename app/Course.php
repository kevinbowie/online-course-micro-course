<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = ['name', 'certificate', 'thumbnail', 'type', 'status', 'price', 'level', 'description', 'mentor_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
    
    public function mentor() {
        return $this->belongsTo(Mentor::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class)->orderBy('id');
    }

    public function images() {
        return $this->hasMany(ImageCourse::class)->orderBy('id', 'DESC');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->orderBy('updated_at', 'DESC');
    }
}
