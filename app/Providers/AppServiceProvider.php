<?php

namespace App\Providers;

use App\Enums\DateFormat;
use App\Enums\TimeFormat;
use App\Models;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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

        /* Unguard models */
        Model::unguard();

        /* Eager load relationships */
        Model::automaticallyEagerLoadRelationships();

        /* Correctness restrictions are always applied */
        Model::preventAccessingMissingAttributes();
        Model::preventSilentlyDiscardingAttributes();

        /* Define morph aliasses */
        Relation::enforceMorphMap([
            'course'     => Models\Course::class,
            'assignment' => Models\Assignment::class,
            'question'   => Models\Question::class,
            'submission' => Models\Submission::class,
            'user'       => Models\User::class,
        ]);

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

        /* Filament */
        FilamentColor::register([
            'danger'  => Color::Red,
            'gray'    => Color::Zinc,
            'info'    => Color::Blue,
            'primary' => Color::Green,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);
    }
}
