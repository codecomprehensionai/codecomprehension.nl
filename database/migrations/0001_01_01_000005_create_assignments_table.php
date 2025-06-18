<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('lti_id')->unique();
            $table->string('lti_lineitem_endpoint');
            $table->foreignUlid('course_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('title');
            $table->text('description')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
