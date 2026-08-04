<?php

namespace Moe\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Profile extends Model
{
    protected $fillable = [
        'profileable_id',
        'profileable_type',
        'key',
        'value',
        'type',
    ];

    public function getTable(): string
    {
        return config('moe-profiles.table', parent::getTable() ?: 'profiles');
    }

    public function profileable(): MorphTo
    {
        return $this->morphTo();
    }
}
