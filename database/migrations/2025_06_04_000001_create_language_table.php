<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguageTable extends Migration
{
    public function up(): void
    {
        Schema::create('Language', function (Blueprint $table) {
            $table->bigInteger('LanguageID')->primary();
            $table->string('LanguageName');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Language');
    }
}