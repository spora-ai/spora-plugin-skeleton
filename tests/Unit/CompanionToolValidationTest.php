<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use Spora\Plugins\Skeleton\CompanionTool;
use Spora\Services\ToolConfigNameResolver;
use Spora\Services\ToolsRecommendsSkillsValidator;
use Spora\Skills\SkillScanner;

/**
 * Materialise a skill directory with a SKILL.md. Mirrors the `writeSkill`
 * helper used by spora-core's SkillScannerTest so this file reads the same
 * way in either repo.
 */
function writeCompanionSkill(string $parent, string $slug, string $frontmatter, string $body): void
{
    $dir = rtrim($parent, '/') . '/' . $slug;
    if (!is_dir($dir) && !mkdir($dir, 0o755, true) && !is_dir($dir)) {
        throw new RuntimeException("Cannot create skill directory: {$dir}");
    }

    $yaml = "---\n" . trim($frontmatter, "\n") . "\n---\n";
    file_put_contents($dir . '/SKILL.md', $yaml . "\n" . ltrim($body, "\n"));
}

/**
 * Build a SkillScanner over a synthesised scan root under sys_get_temp_dir.
 * Returns [scanner, cleanup, root] — callers must invoke cleanup() in a
 * `finally` block.
 *
 * @return array{0: SkillScanner, 1: callable(): void, 2: string}
 */
function makeCompanionSkillScanner(): array
{
    $abs = sys_get_temp_dir() . '/spora_companion_scan_' . uniqid('', true);
    if (!is_dir($abs) && !mkdir($abs, 0o755, true) && !is_dir($abs)) {
        throw new RuntimeException("Cannot create test directory: {$abs}");
    }

    $cleanup = static function () use ($abs): void {
        if (!is_dir($abs)) {
            return;
        }
        $files = [];
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($iter as $f) {
            $files[] = $f->getRealPath();
        }
        foreach ($files as $f) {
            @is_dir($f) ? @rmdir($f) : @unlink($f);
        }
        @rmdir($abs);
    };

    return [
        new SkillScanner([['path' => $abs, 'source' => 'skeleton']]),
        $cleanup,
        $abs,
    ];
}

test('the bundled companion-skill satisfies ToolsRecommendsSkillsValidator', function (): void {
    [$scanner, $cleanup, $root] = makeCompanionSkillScanner();
    try {
        writeCompanionSkill(
            $root,
            'companion-skill',
            "name: companion-skill\ndescription: Demo skill bundled by CompanionTool.",
            "# Companion skill",
        );

        $resolver = new ToolConfigNameResolver(new NullLogger(), [CompanionTool::class]);
        $validator = new ToolsRecommendsSkillsValidator($resolver, $scanner);

        expect($validator->validate())->toBe([]);
    } finally {
        $cleanup();
    }
});

test('a missing recommended slug produces a non-empty error list', function (): void {
    [$scanner, $cleanup] = makeCompanionSkillScanner();
    try {
        $resolver = new ToolConfigNameResolver(new NullLogger(), [CompanionTool::class]);
        $validator = new ToolsRecommendsSkillsValidator($resolver, $scanner);

        expect($validator->validate())->not->toBe([]);
    } finally {
        $cleanup();
    }
});
