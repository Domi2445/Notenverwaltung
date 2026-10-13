<?php

namespace Tests\Unit\Plugins;

use App\Plugins\Plugin;
use App\Plugins\PluginManager;
use InvalidArgumentException;
use Tests\TestCase;

class PluginManagerTest extends TestCase
{
    public function test_plugins_can_be_registered_and_found(): void
    {
        $manager = new PluginManager();
        $plugin = $this->plugin('example.catalog');

        $manager->register($plugin);

        $this->assertTrue($manager->has('example.catalog'));
        $this->assertSame($plugin, $manager->find('example.catalog'));
        $this->assertCount(1, $manager->all());
    }

    public function test_duplicate_plugin_ids_are_rejected(): void
    {
        $manager = new PluginManager();
        $manager->register($this->plugin('example.catalog'));

        $this->expectException(InvalidArgumentException::class);

        $manager->register($this->plugin('example.catalog'));
    }

    private function plugin(string $id): Plugin
    {
        return new class($id) implements Plugin
        {
            public function __construct(private readonly string $id)
            {
            }

            public function id(): string
            {
                return $this->id;
            }

            public function name(): string
            {
                return 'Example';
            }

            public function version(): string
            {
                return '1.0.0';
            }

            public function description(): string
            {
                return 'Example plugin.';
            }

            public function icon(): string
            {
                return '✦';
            }

            public function accent(): string
            {
                return 'primary';
            }

            public function url(): string
            {
                return '/example';
            }
        };
    }
}
