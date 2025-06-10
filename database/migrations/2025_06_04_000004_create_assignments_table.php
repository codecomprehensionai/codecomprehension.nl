<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->timestamp('deadline_at')->nullable();

            $table->timestamps();
        });

        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            /* Question metadata */
            $table->string('level');
            $table->string('type');
            $table->string('language');
            $table->string('topic')->nullable();
            $table->json('tags')->nullable();
            $table->integer('estimated_duration');

            /* Question content */
            $table->text('question')->nullable();
            $table->text('explanation')->nullable();
            $table->text('answer')->nullable();
            $table->text('code')->nullable();
            $table->json('options')->nullable();
        });

        Schema::create('assignment_questions_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_question_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->text('answer');
            $table->text('feedback')->nullable();
            $table->boolean('is_correct')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
