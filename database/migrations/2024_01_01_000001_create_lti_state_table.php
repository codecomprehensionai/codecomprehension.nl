<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lti_states', function (Blueprint $table) {
            $table->id();
            $table->string('state_key', 64)->unique();
            $table->string('nonce', 64);
            $table->text('issuer');
            $table->string('client_id', 100);
            $table->text('login_hint')->nullable();
            $table->text('lti_message_hint')->nullable();
            $table->text('target_link_uri')->nullable();
            $table->string('deployment_id', 500)->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('state_key');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_states');
    }
};
