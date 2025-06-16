<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(match (app()->environment()) {
            'production', 'staging', 'testing' => [
                //
            ],
            'development', 'local' => [
                UserSeeder::class
            ],
            default => throw new Exception('Invalid environment value!'),
        });
    }
}
