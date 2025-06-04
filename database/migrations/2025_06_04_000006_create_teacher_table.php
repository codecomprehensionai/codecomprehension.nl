<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherTable extends Migration
{
    public function up(): void
    {
        Schema::create('Teacher', function (Blueprint $table) {
            $table->bigInteger('UserID')->primary();
            $table->foreign('UserID')
                  ->references('UserID')
                  ->on('User')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Teacher');
    }
}