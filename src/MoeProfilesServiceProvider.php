<?php

namespace Moe\Profiles;

use Illuminate\Support\ServiceProvider;
use Moe\Profiles\Services\ProfileService;

class MoeProfilesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/moe-profiles.php', 'moe-profiles');

        $this->app->singleton('moe.profiles', function ($app) {
            return new ProfileService();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/moe-profiles.php' => config_path('moe-profiles.php'),
            ], 'moe-profiles-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_profiles_table.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_profiles_table.php'),
            ], 'moe-profiles-migration');
        }
    }
}
