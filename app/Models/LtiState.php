<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LtiState extends Model
{
    protected $fillable = [
        'state_key',
        'nonce',
        'issuer',
        'client_id',
        'login_hint',
        'lti_message_hint',
        'target_link_uri',
        'deployment_id',
        'additional_data',
        'expires_at'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'expires_at' => 'datetime'
    ];

    /**
     * Create a new LTI state record
     */
    public static function createState(array $data): self
    {
        // Clean up expired states
        self::cleanupExpired();

        return self::create([
            'state_key' => $data['state'],
            'nonce' => $data['nonce'],
            'issuer' => $data['issuer'],
            'client_id' => $data['client_id'],
            'login_hint' => $data['login_hint'] ?? null,
            'lti_message_hint' => $data['lti_message_hint'] ?? null,
            'target_link_uri' => $data['target_link_uri'] ?? null,
            'deployment_id' => $data['deployment_id'] ?? null,
            'additional_data' => $data['additional_data'] ?? null,
            'expires_at' => Carbon::now()->addMinutes(10) // 10 minute expiry
        ]);
    }

    /**
     * Retrieve and validate state
     */
    public static function validateAndRetrieve(string $state): ?self
    {
        return self::where('state_key', $state)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Clean up expired state records
     */
    public static function cleanupExpired(): void
    {
        self::where('expires_at', '<', Carbon::now())->delete();
    }

    /**
     * Consume (delete) the state after successful validation
     */
    public function consume(): void
    {
        $this->delete();
    }
}
