<?php

namespace App\Models;

use App\Enums\CryptoKeyType;
use App\Services\Crypto\CryptoJwtService;
use App\Services\Crypto\CryptoSodiumService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JwtKey extends Model
{
    use HasUlids;
    use LogsActivity;

    protected $fillable = [
        'name',
        'type',
        'public_key',
        'private_key',
    ];

    protected $hidden = [
        'private_key',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $key) {
            if (blank($key->private_key)) {
                $keyPair = $key->service()->generateKeyPair();

                $key->private_key = $keyPair['private_key'];
                $key->public_key = $keyPair['public_key'];
            }
        });
    }

    public function service(): CryptoJwtService|CryptoSodiumService
    {
        return match ($this->type) {
            CryptoKeyType::JWT    => new CryptoJwtService($this),
            CryptoKeyType::SODIUM => new CryptoSodiumService($this),
            default               => throw new InvalidArgumentException("CryptoKey type invalid: {$this->type?->value}"),
        };
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty()
            ->logAll()
            ->logExcept([...$this->hidden, 'updated_at', 'created_at'])
            ->setDescriptionForEvent(
                fn (string $eventName): string => "[CryptoKey] :subject.name {$eventName} by :causer.name"
            );
    }

    public function scopeType(Builder $query, CryptoKeyType $type): Builder
    {
        return $query->where('type', $type);
    }

    protected function casts(): array
    {
        return [
            'type'        => CryptoKeyType::class,
            'public_key'  => 'encrypted',
            'private_key' => 'encrypted',
        ];
    }
}
