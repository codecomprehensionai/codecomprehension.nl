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
            $table->text('lti_message_hint')->nullable();
            $table->text('target_link_uri');
            $table->string('client_id');
            $table->string('deployment_id');
            $table->string('canvas_region');
            $table->string('canvas_environment');
            $table->ulid('state')->unique();
            $table->ulid('nonce')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lti_sessions');
    }
};
