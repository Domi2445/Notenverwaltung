<?php

namespace App\Plugins;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class PluginManager
{
    /**
     * @var array<string, Plugin>
     */
    private array $plugins = [];

    public function register(Plugin $plugin): void
    {
        if (isset($this->plugins[$plugin->id()])) {
            throw new InvalidArgumentException("Plugin [{$plugin->id()}] is already registered.");
        }

        $this->plugins[$plugin->id()] = $plugin;
    }

    public function all(): Collection
    {
        return collect($this->plugins)->sortBy(fn (Plugin $plugin) => $plugin->name());
    }

    public function find(string $id): ?Plugin
    {
        return $this->plugins[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->plugins[$id]);
    }
}
