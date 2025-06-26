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
            $table->string('lti_id')->required();
            $table->foreignUlid('question_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUlid('user_id')->required()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('answer');
            $table->unsignedInteger('attempt')->default(1);
            $table->text('feedback')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
