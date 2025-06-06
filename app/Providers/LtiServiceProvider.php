<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class LtiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge LTI configuration
        $this->mergeConfigFrom(__DIR__ . '/../../config/lti.php', 'lti');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish LTI configuration
        $this->publishes([
            __DIR__ . '/../../config/lti.php' => config_path('lti.php'),
        ], 'lti-config');

        // Add custom Blade directives for LTI
        Blade::if('lti', function () {
            return session()->has('lti_context');
        });

        Blade::directive('ltiUser', function () {
            return "<?php echo session('lti_user_id', 'Anonymous'); ?>";
        });

        Blade::directive('ltiContext', function ($expression) {
            return "<?php echo session('lti_context')[$expression] ?? 'Not available'; ?>";
        });
    }
}
