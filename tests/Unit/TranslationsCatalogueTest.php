<?php

declare(strict_types=1);

use Spora\Plugins\Skeleton\SkeletonPlugin;

$catalogueDir = dirname(__DIR__, 2) . '/locales';
$catalogueFile = $catalogueDir . '/de.json';
$pluginRoot = dirname(__DIR__, 2);

/**
 * Every English literal the plugin's own source writes, decoded.
 *
 * A catalogue key is the English source string byte for byte, so a key the
 * source no longer produces is an orphan — the copy edit that was never
 * re-translated. The literals are read through the tokenizer rather than
 * searched as raw text, because the source writes an apostrophe as `\'`
 * and a raw substring search would never match the key.
 *
 * The manifest description counts as source: it is the plugin's own
 * English copy and `locales/de.json` translates it.
 *
 * @return list<string>
 */
$sourceLiterals = function (string $root): array {
    $literals = [];

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS),
    );

    foreach ($files as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        foreach (token_get_all((string) file_get_contents($file->getPathname())) as $token) {
            if (!is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }

            $literal = substr($token[1], 1, -1);

            // Single-quoted PHP escapes only `\'` and `\\`; a double-quoted
            // one carries the full escape set.
            $literals[] = $token[1][0] === "'"
                ? str_replace(['\\\\', "\\'"], ['\\', "'"], $literal)
                : (string) stripcslashes($literal);
        }
    }

    $manifest = json_decode((string) file_get_contents($root . '/plugin.json'), true, 512, JSON_THROW_ON_ERROR);
    $literals[] = (string) ($manifest['description'] ?? '');

    return $literals;
};

it('declares the locales directory through translationsPaths()', function () use ($catalogueDir): void {
    $paths = (new SkeletonPlugin())->translationsPaths();

    expect($paths)->toHaveCount(1);
    expect(is_dir($paths[0]))->toBeTrue();
    expect(realpath((string) $paths[0]))->toBe(realpath($catalogueDir));
});

it('decodes locales/de.json into a non-empty map', function () use ($catalogueFile): void {
    expect(is_file($catalogueFile))->toBeTrue();

    $decoded = json_decode((string) file_get_contents($catalogueFile), true, flags: JSON_THROW_ON_ERROR);

    expect($decoded)->toBeArray();
    expect($decoded)->not->toBeEmpty();
    expect(array_is_list($decoded))->toBeFalse();
});

it('never leaves a message untranslated', function () use ($catalogueFile): void {
    /** @var array<string, string> $decoded */
    $decoded = json_decode((string) file_get_contents($catalogueFile), true, flags: JSON_THROW_ON_ERROR);

    foreach ($decoded as $english => $german) {
        expect(trim($german))->not->toBe('', "empty German value for: {$english}");
        expect($german)->not->toBe($english, "untranslated German value for: {$english}");
    }
});

it('never carries a key the source no longer produces', function () use ($catalogueFile, $pluginRoot, $sourceLiterals): void {
    /** @var array<string, string> $decoded */
    $decoded = json_decode((string) file_get_contents($catalogueFile), true, flags: JSON_THROW_ON_ERROR);

    $literals = $sourceLiterals($pluginRoot);

    $orphans = [];

    foreach (array_keys($decoded) as $key) {
        if (!in_array((string) $key, $literals, true)) {
            $orphans[] = $key;
        }
    }

    expect($orphans)->toBe([]);
});
