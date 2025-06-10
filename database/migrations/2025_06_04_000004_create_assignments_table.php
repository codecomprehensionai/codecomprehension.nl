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
            $table->text('title');
            $table->integer('level');
            $table->timestamp('due_date');
            $table->integer('estimated_time');
            $table->json('test');
            $table->bigInteger('language_id');
            $table->json('questions');
            $table->bigInteger('group_id')->nullable();


            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
