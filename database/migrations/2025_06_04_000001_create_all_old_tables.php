<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //
        // 1) Create “Language” table
        //
        Schema::create('Language', function (Blueprint $table) {
            $table->bigInteger('LanguageID')->primary();
            // char[] → json (since Laravel has no char‐array); if you truly want text, change → text()
            $table->string('LanguageName');
        });

        //
        // 2) Create “Group” table
        //
        Schema::create('Group', function (Blueprint $table) {
            $table->bigInteger('GroupID')->primary();
            $table->string('group_name');
        });

        //
        // 3) Create “User” table
        //
        Schema::create('User', function (Blueprint $table) {
            $table->bigInteger('UserID')->primary();
            $table->string('name');
        });

        //
        // 4) Create “Assignment” table
        //
        Schema::create('Assignment', function (Blueprint $table) {
            $table->bigInteger('AssignmentID')->primary();
            $table->text('Title');
            $table->integer('Level');
            $table->timestamp('DueDate');
            $table->integer('EstimatedTime');
            $table->json('Test');
            $table->bigInteger('LanguageID');
            $table->json('Questions');
            $table->bigInteger('GroupID');

            // Foreign keys:
            $table->foreign('LanguageID')
                  ->references('LanguageID')
                  ->on('Language')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('GroupID')
                  ->references('GroupID')
                  ->on('Group')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });

        //
        // 5) Create “Student” table (inherits from “User” in original dump)
        //
        Schema::create('Student', function (Blueprint $table) {
            $table->bigInteger('UserID')->primary();
            $table->foreign('UserID')
                  ->references('UserID')
                  ->on('User')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        //
        // 6) Create “Teacher” table (inherits from “User”)
        //
        Schema::create('Teacher', function (Blueprint $table) {
            $table->bigInteger('UserID')->primary();
            $table->foreign('UserID')
                  ->references('UserID')
                  ->on('User')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        //
        // 7) Create “StudentGroup” join table
        //
        Schema::create('StudentGroup', function (Blueprint $table) {
            $table->bigInteger('ID')->primary();
            $table->bigInteger('StudentID');
            $table->bigInteger('GroupID');

            $table->foreign('StudentID')
                  ->references('UserID')
                  ->on('Student')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('GroupID')
                  ->references('GroupID')
                  ->on('Group')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        //
        // 8) Create “TeacherOf” join table
        //
        Schema::create('TeacherOf', function (Blueprint $table) {
            $table->bigInteger('ID')->primary();
            $table->bigInteger('Group');
            $table->bigInteger('Teacher');

            $table->foreign('Teacher')
                  ->references('UserID')
                  ->on('Teacher')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('Group')
                  ->references('GroupID')
                  ->on('Group')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        //
        // 9) Create “Submission” table
        //
        Schema::create('Submission', function (Blueprint $table) {
            $table->bigInteger('ID')->primary();
            $table->json('Answer');
            // boolean[] → Integer[] (since Laravel has no boolean‐array)
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
        // Drop in reverse‐dependency order:
        Schema::dropIfExists('Submission');
        Schema::dropIfExists('TeacherOf');
        Schema::dropIfExists('StudentGroup');
        Schema::dropIfExists('Teacher');
        Schema::dropIfExists('Student');
        Schema::dropIfExists('Assignment');
        Schema::dropIfExists('User');
        Schema::dropIfExists('Group');
        Schema::dropIfExists('Language');
    }
};
