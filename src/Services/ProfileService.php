<?php

namespace Moe\Profiles\Services;

use Illuminate\Database\Eloquent\Model;
use Moe\Profiles\Models\Profile;

class ProfileService
{
    public function get(Model $profileable, string $key, mixed $default = null): mixed
    {
        $profile = Profile::query()
            ->where('profileable_id', $profileable->getKey())
            ->where('profileable_type', $profileable->getMorphClass())
            ->where('key', $key)
            ->first();

        if (!$profile) {
            return $default;
        }

        return $this->castToPhp($profile->value, $profile->type);
    }

    public function set(Model $profileable, string $key, mixed $value, ?string $type = null): void
    {
        $type = $type ?? $this->inferType($value);

        Profile::query()->updateOrCreate(
            [
                'profileable_id' => $profileable->getKey(),
                'profileable_type' => $profileable->getMorphClass(),
                'key' => $key,
            ],
            [
                'value' => $this->formatForStorage($value, $type),
                'type' => $type,
            ],
        );
    }

    public function getAll(Model $profileable): array
    {
        return Profile::query()
            ->where('profileable_id', $profileable->getKey())
            ->where('profileable_type', $profileable->getMorphClass())
            ->get()
            ->mapWithKeys(function (Profile $profile) {
                return [$profile->key => $this->castToPhp($profile->value, $profile->type)];
            })
            ->all();
    }

    public function setMultiple(Model $profileable, array $data): void
    {
        foreach ($data as $key => $value) {
            $type = null;

            if (is_array($value) && array_key_exists('value', $value)) {
                $type = $value['type'] ?? null;
                $value = $value['value'];
            }

            $this->set($profileable, $key, $value, $type);
        }
    }

    public function has(Model $profileable, string $key): bool
    {
        return Profile::query()
            ->where('profileable_id', $profileable->getKey())
            ->where('profileable_type', $profileable->getMorphClass())
            ->where('key', $key)
            ->exists();
    }

    public function forget(Model $profileable, string $key): void
    {
        Profile::query()
            ->where('profileable_id', $profileable->getKey())
            ->where('profileable_type', $profileable->getMorphClass())
            ->where('key', $key)
            ->delete();
    }

    protected function formatForStorage(mixed $value, string $type): string
    {
        return match ($type) {
            'integer' => (string) (int) $value,
            'float' => (string) (float) $value,
            'boolean' => $value ? '1' : '0',
            'json' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };
    }

    protected function castToPhp(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE) ?? false,
            'json' => is_string($value) ? json_decode($value, true) ?? [] : $value,
            default => $value,
        };
    }

    protected function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }
}
