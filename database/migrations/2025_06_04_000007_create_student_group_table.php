<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentGroupTable extends Migration
{
    public function up(): void
    {
        Schema::create('student_groups', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('student_id');
            $table->bigInteger('group_id');

            $table->foreign('student_id')
                  ->references('user_id')
                  ->on('students')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_groups');
    }
};