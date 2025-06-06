<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateLtiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lti:generate-keys {--force : Force regeneration of existing keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RSA key pair for LTI 1.3 authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $privateKeyPath = storage_path('app/private/lti_private_key.pem');
        $publicKeyPath = storage_path('app/public/lti_public_key.pem');

        // Create directories if they don't exist
        File::ensureDirectoryExists(dirname($privateKeyPath));
        File::ensureDirectoryExists(dirname($publicKeyPath));

        // Check if keys already exist
        if (File::exists($privateKeyPath) && File::exists($publicKeyPath) && !$this->option('force')) {
            $this->info('LTI keys already exist. Use --force to regenerate.');
            return;
        }

        try {
            // Generate RSA key pair
            $config = [
                'digest_alg' => 'sha256',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            $this->info('Generating RSA key pair...');

            // Create private key
            $privateKey = openssl_pkey_new($config);
            if (!$privateKey) {
                throw new \Exception('Failed to generate private key: ' . openssl_error_string());
            }

            // Export private key
            openssl_pkey_export($privateKey, $privateKeyOut);
            File::put($privateKeyPath, $privateKeyOut);

            // Extract public key
            $publicKeyDetails = openssl_pkey_get_details($privateKey);
            $publicKeyOut = $publicKeyDetails['key'];
            File::put($publicKeyPath, $publicKeyOut);

            // Set proper permissions
            chmod($privateKeyPath, 0600);
            chmod($publicKeyPath, 0644);

            $this->info('LTI RSA key pair generated successfully!');
            $this->line('Private key: ' . $privateKeyPath);
            $this->line('Public key: ' . $publicKeyPath);

            // Display public key for LMS configuration
            $this->line('');
            $this->info('Copy the following public key to your LMS configuration:');
            $this->line('');
            $this->line($publicKeyOut);
        } catch (\Exception $e) {
            $this->error('Failed to generate LTI keys: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
