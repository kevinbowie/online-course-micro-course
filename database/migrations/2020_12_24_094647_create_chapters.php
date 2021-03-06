<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapters extends Migration
{
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chapters');
    }
}
