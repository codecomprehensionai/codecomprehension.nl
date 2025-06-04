<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionTable extends Migration
{
    public function up(): void
    {
        Schema::create('Submission', function (Blueprint $table) {
            $table->bigInteger('ID')->primary();
            $table->json('Answer');
            $table->integer('CorrectAnswer');
            $table->bigInteger('StudentID');
            $table->bigInteger('TeacherID');

            $table->foreign('StudentID')
                  ->references('UserID')
                  ->on('Student')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('TeacherID')
                  ->references('UserID')
                  ->on('Teacher')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Submission');
    }
};