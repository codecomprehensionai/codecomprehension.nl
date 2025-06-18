<?php

namespace App\Services\Jwt;

use Carbon\CarbonImmutable;
use Psr\Clock\ClockInterface;

class JwtClock implements ClockInterface
{
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }
}
