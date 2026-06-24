<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use DI\ContainerBuilder;
use Spora\Plugins\PluginInterface;
use Spora\Plugins\Skeleton\Tools\EchoTool;

final class SkeletonPlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'Skeleton';
    }

    /** @return array<string, string> */
    public function autoload(): array
    {
        return ['Spora\\Plugins\\Skeleton\\' => __DIR__];
    }

    /** @return array<class-string<\Spora\Tools\ToolInterface>> */
    public function tools(): array
    {
        return [
            EchoTool::class,
        ];
    }

    /** @return array<string, class-string<\Spora\Drivers\LLMDriverInterface>> */
    public function drivers(): array
    {
        return [];
    }

    /** @return string[] */
    public function recipePaths(): array
    {
        return [];
    }

    public function schemaVersion(): int
    {
        return 0;
    }

    public function migrationsPath(): ?string
    {
        return null;
    }

    public function register(ContainerBuilder $builder): void
    {
        // Add DI bindings here when the plugin needs to inject custom services.
    }
}
