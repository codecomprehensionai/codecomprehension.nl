<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTable extends Migration
{
    public function up(): void
    {
        Schema::create('Group', function (Blueprint $table) {
            $table->bigInteger('GroupID')->primary();
            $table->string('group_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Group');
    }
}