<?php

namespace App\Providers;

use App\Enums\DateFormat;
use App\Enums\TimeFormat;
use App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* Enforce HTTPS */
        URL::forceHttps(app()->environment(['production', 'staging']));

        /* Destructive restrictions are applied in production and staging */
        DB::prohibitDestructiveCommands(app()->environment(['production', 'staging']));

        /* Eager load relationships */
        Model::automaticallyEagerLoadRelationships();

        /* Correctness restrictions are always applied */
        Model::preventAccessingMissingAttributes();
        Model::preventSilentlyDiscardingAttributes();

        /* Define morph aliasses */
        Relation::enforceMorphMap([
            'token' => Models\Token::class,
            'user'  => Models\User::class,
        ]);

        /* Sanctum */
        Sanctum::usePersonalAccessTokenModel(Models\Token::class);

        /* Carbon */
        Carbon::macro('inTimezone', function () {
            /** @var \Carbon\Carbon $this */
            return $this->tz(config('app.timezone_display'));
        });
        Carbon::macro('formatDate', function () {
            /** @var \Carbon\Carbon $this */
            return $this->translatedFormat(DateFormat::DEFAULT);
        });
        Carbon::macro('formatTime', function () {
            /** @var \Carbon\Carbon $this */
            return $this->translatedFormat(TimeFormat::DEFAULT);
        });
        Carbon::macro('formatDateTime', function () {
            /** @var \Carbon\Carbon $this */
            return $this->translatedFormat(DateFormat::DEFAULT_WITH_TIME);
        });

        /* Mail */
        if (!app()->isProduction()) {
            Mail::alwaysTo('luca@castelnuovo.dev');
        }
    }
}
