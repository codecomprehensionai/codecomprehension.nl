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
            $table->foreignId('question_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->text('answer');
            $table->text('feedback')->nullable();
            $table->boolean('is_correct')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
