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
            $table->integer('order');
            $table->string('language');
            $table->string('type');
            $table->string('level');
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->float('score_max')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
