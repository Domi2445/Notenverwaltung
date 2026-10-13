<?php

namespace App\Console\Commands;

use App\Plugins\PluginManager;
use Illuminate\Console\Command;

class ListPluginsCommand extends Command
{
    protected $signature = 'platform:plugins';

    protected $description = 'List all registered platform plugins';

    public function handle(PluginManager $manager): int
    {
        $plugins = $manager->all();

        if ($plugins->isEmpty()) {
            $this->info('No plugins are registered.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Version', 'Description'],
            $plugins->map(fn ($plugin) => [
                $plugin->id(),
                $plugin->name(),
                $plugin->version(),
                $plugin->description(),
            ])->all(),
        );

        return self::SUCCESS;
    }
}
