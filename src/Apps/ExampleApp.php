<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton\Apps;

use Spora\Apps\AppInterface;

/**
 * Example app — the shape a plugin's admin/UI panel takes.
 *
 * **Not registered.** `SkeletonPlugin::apps()` returns `[]`, so this class
 * ships as a reference to copy rather than a live panel: registering it would
 * put a nav entry in the operator's sidebar that routes nowhere.
 *
 * To adopt it, return `[ExampleApp::class]` from `apps()`, add a Vue app under
 * `frontend/src/apps/`, and register that in the host's app registry.
 *
 * `displayName()` and `description()` are operator-facing and therefore
 * translatable — they are among the few strings Spora localizes on the PHP
 * side. Put their German in `locales/de.json`, keyed by the English text.
 */
final class ExampleApp implements AppInterface
{
    public function name(): string
    {
        return 'example';
    }

    public function displayName(): string
    {
        return 'Example';
    }

    public function description(): string
    {
        return 'Example panel shipped by the skeleton — replace or remove.';
    }

    public function icon(): string
    {
        return 'layout-dashboard';
    }

    public function accent(): string
    {
        return 'slate';
    }
}
