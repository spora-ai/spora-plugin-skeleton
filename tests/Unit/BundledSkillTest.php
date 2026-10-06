<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use Spora\Plugins\Skeleton\SkeletonPlugin;
use Spora\Services\ToolConfigNameResolver;
use Spora\Skills\Skill;
use Spora\Skills\SkillScanner;
use Spora\Tools\Attributes\Tool;

/**
 * `skills/companion-skill/SKILL.md` states its own contract in prose:
 * "The slug declared on `CompanionTool::recommendsSkills` must match this
 * directory name". Prose is not enforcement, so a fork could rename one side
 * and ship a plugin whose tool points at a skill core cannot resolve, failing
 * only at runtime with `TOOLS_RECOMMENDS_SKILLS_MISSING`.
 *
 * These tests pin the pairing in both directions so the template cannot
 * silently violate what its own SKILL.md claims. They read the tool registry
 * and the skill roots from {@see SkeletonPlugin} rather than hard-coding
 * names, so a fork that adds tools or skills gets the checks for free, and
 * they delegate parsing to core's {@see SkillScanner} / validator so the rules
 * applied here are the rules production applies.
 */

/**
 * Every `#[Tool]` the plugin registers, keyed by its wire name.
 *
 * @return array<string, Tool>
 */
function skeletonRegisteredTools(): array
{
    $tools = [];

    foreach ((new SkeletonPlugin())->tools() as $class) {
        foreach ((new ReflectionClass($class))->getAttributes(Tool::class) as $attribute) {
            $tool = $attribute->newInstance();
            $tools[$tool->name] = $tool;
        }
    }

    return $tools;
}

/**
 * Slugs declared across every registered tool via `recommendsSkills`.
 *
 * @return list<string>
 */
function skeletonRecommendedSkillSlugs(): array
{
    $slugs = [];

    foreach (skeletonRegisteredTools() as $tool) {
        foreach ($tool->getRecommendsSkills() as $slug) {
            $slugs[] = $slug;
        }
    }

    return array_values(array_unique($slugs));
}

/**
 * Scan the plugin's real `skills/` roots through core's own scanner.
 *
 * The resolver is wired in so core validates `allowed-tools` against the
 * installed tool set rather than skipping that check.
 *
 * @return list<Skill>
 */
function skeletonBundledSkills(): array
{
    $roots = [];

    foreach ((new SkeletonPlugin())->skillPaths() as $path) {
        $roots[] = ['path' => $path, 'source' => 'skeleton'];
    }

    $resolver = new ToolConfigNameResolver(new NullLogger(), (new SkeletonPlugin())->tools());

    return (new SkillScanner($roots, $resolver))->scan();
}

it('declares at least one recommended skill slug', function (): void {
    expect(skeletonRecommendedSkillSlugs())->not->toBe([]);
});

it('backs every recommended skill slug with a skill directory that ships', function (): void {
    $onDisk = array_map(static fn(Skill $skill): string => $skill->slug(), skeletonBundledSkills());

    $orphans = array_values(array_diff(skeletonRecommendedSkillSlugs(), $onDisk));

    expect($orphans)->toBe(
        [],
        "#[Tool(recommendsSkills: ...)] declares slugs with no matching directory under 'skills/'. "
            . 'A tool must recommend a skill it actually bundles.',
    );
});

it('ships no skill directory that no registered tool recommends', function (): void {
    $declared = skeletonRecommendedSkillSlugs();

    $unclaimed = array_values(array_filter(
        skeletonBundledSkills(),
        static fn(Skill $skill): bool => ! in_array($skill->slug(), $declared, true),
    ));

    expect(array_map(static fn(Skill $skill): string => $skill->slug(), $unclaimed))->toBe(
        [],
        "Every skill under 'skills/' must be claimed by some tool's recommendsSkills, "
            . 'otherwise a fork can ship a skill the operator UI never offers to enable.',
    );
});

it('bundles skills whose frontmatter core accepts', function (): void {
    foreach (skeletonBundledSkills() as $skill) {
        // Advisory warnings are deliberately not asserted on: core downgrades
        // `ALLOWED_TOOLS_UNKNOWN_TOOL` to a warning because a bundled skill may
        // legitimately name a core-provided tool that this plugin does not
        // register, and asserting on it would fail a correct fork.
        $errors = array_values(array_filter(
            $skill->warnings(),
            static fn(array $entry): bool => $entry['severity'] === 'error',
        ));

        expect($errors)->toBe(
            [],
            "Skill '{$skill->slug()}' failed core's SKILL.md validation, which covers the "
                . 'frontmatter `name:` matching its directory name and the shape of `allowed-tools`.',
        );
    }
});
