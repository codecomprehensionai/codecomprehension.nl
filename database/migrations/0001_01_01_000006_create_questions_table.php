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

            $table->string('language');
            $table->string('type');
            $table->string('level');

            $table->text('question');
            $table->text('answer');
            $table->integer("score_max");

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
