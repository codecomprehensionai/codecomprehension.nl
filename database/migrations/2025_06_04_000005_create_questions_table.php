<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
