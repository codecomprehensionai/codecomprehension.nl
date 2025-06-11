<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lti_sessions', function (Blueprint $table) {
            $table->id();

            $table->text('iss');
            $table->text('login_hint');
            $table->text('target_link_uri');
            $table->string('client_id');
            $table->string('deployment_id');
            $table->string('canvas_region');
            $table->string('canvas_environment');

            $table->string('state')->unique();
            $table->string('nonce');

            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lti_sessions');
    }
};
