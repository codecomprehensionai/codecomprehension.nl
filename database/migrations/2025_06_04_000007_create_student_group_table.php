<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentGroupTable extends Migration
{
    public function up(): void
    {
        Schema::create('StudentGroup', function (Blueprint $table) {
            $table->bigInteger('ID')->primary();
            $table->bigInteger('StudentID');
            $table->bigInteger('GroupID');

            $table->foreign('StudentID')
                  ->references('UserID')
                  ->on('Student')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('GroupID')
                  ->references('GroupID')
                  ->on('Group')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('StudentGroup');
    }
};