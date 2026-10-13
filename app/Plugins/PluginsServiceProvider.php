<?php

namespace App\Plugins;

use Illuminate\Support\ServiceProvider;

class PluginsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginManager::class);
    }

    public function boot(PluginManager $manager): void
    {
        foreach (config('plugins.manifests', []) as $manifest) {
            $plugin = $manifest instanceof Plugin ? $manifest : app()->make($manifest);
            $manager->register($plugin);
        }
    }
}
