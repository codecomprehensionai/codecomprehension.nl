<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'ddd', 'die', 'dump', 'ray', 'sleep'])
    ->each->not->toBeUsed();

arch('it will use strict types')
    ->skip('Skipped for now.')
    ->expect('App')
    ->toUseStrictTypes();

arch('Commands')
    ->expect('App\Console\Commands')
    ->toHaveSuffix('Command')
    ->toBeClasses()
    ->toExtend(Illuminate\Console\Command::class)
    ->toHaveMethod('handle');

arch('Data')
    ->expect('App\Data')
    ->toHaveSuffix('Data')
    ->toBeClasses()
    ->toExtend(Spatie\LaravelData\Data::class);

arch('Enums')
    ->expect('App\Enums')
    // ->toHaveSuffix('Enum')
    ->toBeEnums();

arch('Controllers')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller')
    ->toBeClasses()
    ->toExtendNothing();

arch('Middleware')
    ->expect('App\Http\Middleware')
    // ->toHaveSuffix('Middleware')
    ->toBeClasses()
    ->toHaveMethod('handle');

arch('Jobs')
    ->expect('App\Jobs')
    ->toHaveSuffix('Job')
    ->toBeClasses()
    ->toImplement(Illuminate\Contracts\Queue\ShouldQueue::class)
    ->toHaveMethod('handle');

arch('Resources')
    ->expect('App\Http\Resources')
    // ->toHaveSuffix('Resource' OR 'Collection')
    ->toBeClasses()
    ->toExtend(Illuminate\Http\Resources\Json\JsonResource::class);

arch('Models')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Illuminate\Database\Eloquent\Model::class);

arch('Policies')
    ->expect('App\Policies')
    ->toHaveSuffix('Policy')
    ->toBeClasses();

arch('Providers')
    ->expect('App\Providers')
    ->toHaveSuffix('Provider')
    ->toBeClasses()
    ->toExtend(Illuminate\Support\ServiceProvider::class);

arch('Traits')
    ->expect('App\Trait')
    // ->toHaveSuffix('Trait')
    ->toBeTraits();
