<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    public function up(): void
    {
        Schema::create('User', function (Blueprint $table) {
            $table->bigInteger('UserID')->primary();
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('User');
    }
}