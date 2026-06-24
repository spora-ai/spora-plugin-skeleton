<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use Spora\Plugins\AbstractPlugin;
use Spora\Plugins\Skeleton\Tools\EchoTool;

/**
 * Plugin entry point — extending {@see AbstractPlugin} (rather than directly
 * implementing {@see \Spora\Plugins\PluginInterface}) means we only have to
 * override the two hooks we actually use: getName() and tools().
 *
 * The base class provides no-op defaults for autoload(), drivers(),
 * recipePaths(), schemaVersion(), migrationsPath(), and register().
 */
final class SkeletonPlugin extends AbstractPlugin
{
    public function getName(): string
    {
        return 'Skeleton';
    }

    /** @return array<class-string<\Spora\Tools\ToolInterface>> */
    public function tools(): array
    {
        return [
            EchoTool::class,
        ];
    }
}
