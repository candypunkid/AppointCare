<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = base_path('Modules');

        if (! is_dir($modulesPath)) {
            return;
        }

        $dirs = scandir($modulesPath);

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $class = "Modules\\{$dir}\\Providers\\{$dir}ServiceProvider";

            if (class_exists($class)) {
                $this->app->register($class);
            }
        }
    }
}
