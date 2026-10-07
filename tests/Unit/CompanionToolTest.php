<?php

declare(strict_types=1);

use Spora\Plugins\Skeleton\CompanionTool;
use Spora\Tools\Attributes\Tool;

it('declares the #Tool attribute on CompanionTool', function (): void {
    $reflection = new ReflectionClass(CompanionTool::class);

    $attrs = $reflection->getAttributes(Tool::class);

    expect($attrs)->toHaveCount(1);
});

it('recommends the bundled companion-skill slug', function (): void {
    $reflection = new ReflectionClass(CompanionTool::class);

    $args = $reflection->getAttributes(Tool::class)[0]->getArguments();

    expect($args['recommendsSkills'] ?? [])->toContain('companion-skill');
})->skip(
    ! property_exists(Tool::class, 'recommendsSkills'),
    'The resolved spora-core predates #[Tool(recommendsSkills: ...)] (added in v0.29.0 by '
        . 'spora-core#269). composer.json floors core at >=0.29.0, so this only fires when a '
        . 'stale install (e.g. an old composer.lock) is still on the vendor directory. '
        . 'Re-enable by deleting this guard once the operator has run `composer update`.',
);
