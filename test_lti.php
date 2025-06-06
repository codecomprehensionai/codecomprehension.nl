<?php

/**
 * LTI 1.3 Integration Test Script
 * 
 * This script tests the basic LTI endpoints to ensure they're working correctly.
 */

require_once 'vendor/autoload.php';

$baseUrl = 'http://localhost:8000'; // Adjust if different

echo "🔍 Testing LTI 1.3 Integration\n";
echo "================================\n\n";

// Test 1: JWKS Endpoint
echo "1. Testing JWKS Endpoint...\n";
$jwksUrl = $baseUrl . '/auth/jwks';
$response = file_get_contents($jwksUrl);

if ($response) {
    $jwks = json_decode($response, true);
    if (isset($jwks['keys']) && count($jwks['keys']) > 0) {
        echo "✅ JWKS endpoint working - Found " . count($jwks['keys']) . " key(s)\n";
        echo "   Key ID: " . ($jwks['keys'][0]['kid'] ?? 'No kid') . "\n";
    } else {
        echo "❌ JWKS endpoint returned invalid data\n";
    }
} else {
    echo "❌ JWKS endpoint not accessible\n";
}

echo "\n2. Testing Tool Configuration Endpoint...\n";
$configUrl = $baseUrl . '/lti/config';
$configResponse = file_get_contents($configUrl);

if ($configResponse) {
    $config = json_decode($configResponse, true);
    if (isset($config['title'])) {
        echo "✅ Tool configuration endpoint working\n";
        echo "   Title: " . $config['title'] . "\n";
        echo "   OIDC URL: " . $config['oidc_initiation_url'] . "\n";
    } else {
        echo "❌ Tool configuration returned invalid data\n";
    }
} else {
    echo "❌ Tool configuration endpoint not accessible\n";
}

echo "\n3. Testing Basic Routing...\n";
$homeResponse = file_get_contents($baseUrl);
if ($homeResponse) {
    echo "✅ Basic routing working\n";
} else {
    echo "❌ Basic routing not working\n";
}

echo "\n4. Checking RSA Keys...\n";
$privateKeyPath = __DIR__ . '/storage/app/private/lti_private_key.pem';
$publicKeyPath = __DIR__ . '/storage/app/public/lti_public_key.pem';

if (file_exists($privateKeyPath) && file_exists($publicKeyPath)) {
    echo "✅ RSA keys exist\n";
    $privateKey = file_get_contents($privateKeyPath);
    $publicKey = file_get_contents($publicKeyPath);

    if (strpos($privateKey, '-----BEGIN PRIVATE KEY-----') !== false) {
        echo "✅ Private key format valid\n";
    } else {
        echo "❌ Private key format invalid\n";
    }

    if (strpos($publicKey, '-----BEGIN PUBLIC KEY-----') !== false) {
        echo "✅ Public key format valid\n";
    } else {
        echo "❌ Public key format invalid\n";
    }
} else {
    echo "❌ RSA keys missing\n";
    echo "   Run: php artisan lti:generate-keys\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Start Laravel server: php artisan serve\n";
echo "2. Set up ngrok or similar for HTTPS: ngrok http 8000\n";
echo "3. Configure Canvas Developer Key with your public URLs\n";
echo "4. Test the complete OIDC flow\n\n";

echo "📋 Canvas Configuration URLs (update with your domain):\n";
echo "- OIDC Initiation: https://your-domain.com/auth/oidc\n";
echo "- Target Link URI: https://your-domain.com/\n";
echo "- Public JWK URL: https://your-domain.com/auth/jwks\n";
echo "- Redirect URIs: https://your-domain.com/auth/launch\n";
