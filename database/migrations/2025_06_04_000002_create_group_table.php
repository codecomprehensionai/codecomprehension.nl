<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTable extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('group_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
}