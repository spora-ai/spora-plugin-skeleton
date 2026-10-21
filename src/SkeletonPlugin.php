<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use Spora\Plugins\AbstractPlugin;
use Spora\Plugins\Skeleton\Tools\EchoTool;

/**
 * The plugin's entry point — `plugin.json`'s `class` must name this FQCN.
 *
 * Extending {@see AbstractPlugin} rather than implementing
 * {@see \Spora\Plugins\PluginInterface} leaves every hook the plugin does not
 * use at its default.
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
            CompanionTool::class,
        ];
    }

    /**
     * Skill directories this plugin ships. SkillScanner walks each depth-1.
     * Remove this method if your plugin ships none.
     *
     * @return string[]
     */
    public function skillPaths(): array
    {
        return [
            __DIR__ . '/../skills',
        ];
    }

    /**
     * Agent-template files this plugin ships. Remove this method if your
     * plugin ships none.
     *
     * @return string[]
     */
    public function agentTemplatePaths(): array
    {
        return [
            __DIR__ . '/../agent-templates',
        ];
    }

    /**
     * The directories holding this plugin's translation catalogues, one
     * `<lang>.json` per language.
     *
     * Which strings translate, and whose catalogue wins a collision, is
     * spelled out in the README's [Translations](../README.md#translations)
     * section.
     *
     * @return string[]
     */
    public function translationsPaths(): array
    {
        return [
            __DIR__ . '/../locales',
        ];
    }
}
