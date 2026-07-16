<?php

namespace Moe\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $fillable = [
        'profileable_id',
        'profileable_type',
        'key',
        'value',
        'type',
    ];

    public function profileable(): MorphTo
    {
        return $this->morphTo();
    }
}
