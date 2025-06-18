<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('assignment_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            /* Question metadata */
            $table->string('language');
            $table->string('type');
            $table->string('level');
            $table->integer('estimated_answer_duration');

            /* Question aidata */
            $table->text('topic')->nullable();
            $table->json('tags')->nullable();

            /* Question content */
            $table->text('question')->nullable();
            $table->text('explanation')->nullable();
            $table->text('code')->nullable();
            $table->json('options')->nullable();
            $table->text('answer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
