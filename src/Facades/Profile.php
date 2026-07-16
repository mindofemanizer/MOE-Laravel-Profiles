<?php

namespace Moe\Profiles\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(\Illuminate\Database\Eloquent\Model $profileable, string $key, mixed $default = null)
 * @method static void set(\Illuminate\Database\Eloquent\Model $profileable, string $key, mixed $value, ?string $type = null)
 * @method static array getAll(\Illuminate\Database\Eloquent\Model $profileable)
 * @method static void setMultiple(\Illuminate\Database\Eloquent\Model $profileable, array $data)
 * @method static bool has(\Illuminate\Database\Eloquent\Model $profileable, string $key)
 * @method static void forget(\Illuminate\Database\Eloquent\Model $profileable, string $key)
 */
class Profile extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'moe.profiles';
    }
}
