<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->json('answer');
            $table->integer('correct_answer');
            $table->bigInteger('student_id');
            $table->bigInteger('teacher_id');
            $table->string('feedback')->nullable();

            $table->foreign('student_id')
                ->references('user_id')
                ->on('students')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('teacher_id')
                ->references('user_id')
                ->on('teachers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
