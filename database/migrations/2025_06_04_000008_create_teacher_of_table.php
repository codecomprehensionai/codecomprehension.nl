<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherOfTable extends Migration
{
    public function up(): void
    {
        Schema::create('TeacherOf', function (Blueprint $table) {
            $table->bigInteger('ID')->primary();
            $table->bigInteger('Group');
            $table->bigInteger('Teacher');

            $table->foreign('Teacher')
                  ->references('UserID')
                  ->on('Teacher')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('Group')
                  ->references('GroupID')
                  ->on('Group')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TeacherOf');
    }
};