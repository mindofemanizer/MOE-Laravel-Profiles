<?php

namespace Moe\Profiles\Traits;

use Moe\Profiles\Models\Profile as ProfileModel;

trait HasProfiles
{
    public function profiles()
    {
        return $this->morphMany(ProfileModel::class, 'profileable');
    }

    public function getProfile(string $key, mixed $default = null): mixed
    {
        return \Profile::get($this, $key, $default);
    }

    public function setProfile(string $key, mixed $value, ?string $type = null): void
    {
        \Profile::set($this, $key, $value, $type);
    }

    public function getAllProfiles(): array
    {
        return \Profile::getAll($this);
    }

    public function setMultipleProfiles(array $data): void
    {
        \Profile::setMultiple($this, $data);
    }

    public function hasProfile(string $key): bool
    {
        return \Profile::has($this, $key);
    }

    public function forgetProfile(string $key): void
    {
        \Profile::forget($this, $key);
    }
}
