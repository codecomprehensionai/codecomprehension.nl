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
            $table->string('state_key', 64)->unique(); // The state parameter
            $table->string('nonce', 64);
            $table->text('issuer'); // Changed to TEXT for longer URLs
            $table->string('client_id', 100); // Increased size for client IDs
            $table->text('login_hint')->nullable(); // Changed to TEXT for longer hints
            $table->text('lti_message_hint')->nullable(); // Changed to TEXT for JWT tokens
            $table->text('target_link_uri')->nullable(); // Changed to TEXT for longer URIs
            $table->string('deployment_id', 500)->nullable(); // Increased size for deployment IDs
            $table->json('additional_data')->nullable(); // For canvas-specific params
            $table->timestamp('expires_at');
            $table->timestamps();

            // Indexes for performance
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
