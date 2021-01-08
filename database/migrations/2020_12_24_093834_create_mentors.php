<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMentors extends Migration
{
    public function up()
    {
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile');
            $table->string('email');
            $table->string('profession');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mentors');
    }
}
