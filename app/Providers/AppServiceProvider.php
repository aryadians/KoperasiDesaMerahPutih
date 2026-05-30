<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use App\Models\SystemConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('system_configs')) {
            try {
                foreach (SystemConfig::all() as $config) {
                    $keyMap = [
                        'APP_NAME' => 'app.name',
                        'APP_ENV' => 'app.env',
                        'APP_DEBUG' => 'app.debug',
                        'SESSION_DRIVER' => 'session.driver',
                        'SESSION_LIFETIME' => 'session.lifetime',
                        'DB_HOST' => 'database.connections.mysql.host',
                        'DB_PORT' => 'database.connections.mysql.port',
                        'DB_DATABASE' => 'database.connections.mysql.database',
                        'DB_USERNAME' => 'database.connections.mysql.username',
                    ];

                    if (isset($keyMap[$config->key])) {
                        $val = $config->value;
                        if ($config->key === 'APP_DEBUG') {
                            $val = ($val === 'true' || $val === '1' || $val === true);
                        }
                        config([$keyMap[$config->key] => $val]);
                    }
                }
            } catch (\Exception $e) {
                // Prevent boot failure if database connection is not established yet
            }
        }
    }
}
