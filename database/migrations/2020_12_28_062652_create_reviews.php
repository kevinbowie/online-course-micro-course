<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviews extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('rating')->default(1);
            $table->longText('note')->nullable();
            $table->unique(['user_id', 'course_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
