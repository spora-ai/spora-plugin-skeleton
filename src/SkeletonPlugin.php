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
     * a string through **the host's** catalogue first, then yours, then
     * the English literal itself, so shipping a catalogue is purely
     * additive: nothing breaks while `de.json` is missing or partial. On
     * a collision the host wins — your entry for a key the host also has
     * is dropped, with a warning in the log — so a plugin can only add a
     * key, never shadow one.
     *
     * Keys are the English source text — `de.json` reads
     * `{"Hello": "Hallo"}`, not `{"greeting": "Hallo"}`. That is what makes
     * a copy edit detectable: the key changes, the old entry orphans.
     * Nothing checks that for you — the host's `i18n:check` harvests the
     * framework's own sources and compares them with the framework's own
     * catalogues, so it is not a plugin gate and an orphan stays inert
     * until your own CI looks. `tests/Unit/TranslationsCatalogueTest.php`
     * is that check.
     *
     * **Not everything English belongs in here.** A string the model reads
     * is never translated — not `#[Tool].description`, not
     * `#[ToolParameter].description`, not `#[ToolSetting].label` or its
     * `options`, and not `ToolResult` content. `locales/de.json` here holds
     * only strings an operator ever sees: the two `#[ToolSetting]`
     * descriptions, the example app's description, and the manifest's.
     * See `CompanionTool` for the annotated version of the rule.
     *
     * The hook needs a `spora-core` that has it. An older core simply
     * never calls the method, so the catalogue is silently ignored and the
     * plugin renders English — which is what the permissive
     * `>=0.30.0 <2.0.0` range in `composer.json` buys.
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
