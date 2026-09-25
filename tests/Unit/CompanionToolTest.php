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

    /** @var Tool $tool */
    $tool = $reflection->getAttributes(Tool::class)[0]->newInstance();

    expect($tool->recommendsSkills ?? [])->toContain('companion-skill');
});
