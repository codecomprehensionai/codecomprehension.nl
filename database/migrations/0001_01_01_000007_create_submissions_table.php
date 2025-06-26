<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('lti_id')->nullable()->unique();
            $table->foreignUlid('question_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            /* User submission */
            $table->text('answer');

            /* LLM grading */
            $table->text('feedback')->nullable();
            $table->float('score')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
