<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentTable extends Migration
{
    public function up(): void
    {
        Schema::create('Assignment', function (Blueprint $table) {
            $table->bigInteger('AssignmentID')->primary();
            $table->text('Title');
            $table->integer('Level');
            $table->timestamp('DueDate');
            $table->integer('EstimatedTime');
            $table->json('Test');
            $table->bigInteger('LanguageID');
            $table->json('Questions');
            $table->bigInteger('GroupID');

            // Foreign keys:
            $table->foreign('LanguageID')
                  ->references('LanguageID')
                  ->on('Language')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('GroupID')
                  ->references('GroupID')
                  ->on('Group')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Assignment');
    }
};