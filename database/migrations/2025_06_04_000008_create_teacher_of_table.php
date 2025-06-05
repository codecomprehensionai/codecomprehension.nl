<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherOfTable extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_of', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('group_id');
            $table->bigInteger('teacher_id');

            $table->foreign('teacher_id')
                  ->references('user_id')
                  ->on('teachers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_of');
    }
};