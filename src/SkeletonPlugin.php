<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use Spora\Plugins\AbstractPlugin;
use Spora\Plugins\Skeleton\Tools\EchoTool;

/**
 * Plugin entry point — extending {@see AbstractPlugin} (rather than directly
 * implementing {@see \Spora\Plugins\PluginInterface}) means we only have to
 * override the hooks we actually use. This skeleton ships tools, skills,
 * and agent templates as the worked example.
 *
 * The base class provides no-op defaults for autoload(), drivers(),
 * recipePaths(), schemaVersion(), migrationsPath(), apps(), routes(),
 * boot(), and register().
 *
 * `src/Apps/ExampleApp.php` is the reference for the `apps()` hook. It is
 * deliberately left unregistered — see that file for how to adopt it.
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
     * Skills this plugin ships. SkillScanner walks depth-1; remove this
     * method if your plugin ships none.
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
     * Agent-template files this plugin ships. Remove if your plugin
     * ships none.
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
     * Translation catalogues this plugin ships. Remove if your plugin
     * ships none — a plugin with no catalogue simply renders English.
     *
     * Each directory holds one `<lang>.json` per language. Spora resolves
     * a string through **your** catalogue first, then the host's, then the
     * English literal itself, so shipping a catalogue is purely additive:
     * nothing breaks while `de.json` is missing or partial.
     *
     * Keys are the English source text — `de.json` reads
     * `{"Hello": "Hallo"}`, not `{"greeting": "Hallo"}`. That is what makes
     * the host's CI able to fail when a string is edited without being
     * re-translated: the key changes, the old entry orphans.
     *
     * **Not everything English belongs in here.** A string the model reads
     * is never translated — not `#[Tool].description`, not
     * `#[ToolParameter].description`, not `#[ToolSetting].label` or its
     * `options`, and not `ToolResult` content. `locales/de.json` here holds
     * exactly three entries, and each one is a string only an operator
     * ever sees. See `CompanionTool` for the annotated version of the rule.
     *
     * Requires `spora-core >= 0.31.0` at runtime; older versions fail to
     * resolve the method when the loader is strict.
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
