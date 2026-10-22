<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use Spora\Services\PrincipalContext;
use Spora\Tools\AbstractTool;
use Spora\Tools\Attributes\Tool;
use Spora\Tools\Attributes\ToolSetting;
use Spora\Tools\ValueObjects\ToolResult;

/**
 * Fork-and-go example that ships a tool + skill pair.
 *
 * `#[Tool(recommendsSkills: ...)]` declares the skills this tool bundles:
 * enabling the tool adds the slug to the Skill tool's allowlist. The slug
 * MUST match the skill directory name — renaming one without the other fails
 * core's strict-mode check with `TOOLS_RECOMMENDS_SKILLS_MISSING`, and
 * `tests/Unit/BundledSkillTest.php` enforces the pairing at build time.
 */
#[Tool(
    name: 'companion',
    description: 'Demo tool that bundles a skill — fork-and-go example.',
    recommendsSkills: ['companion-skill'],
)]
// Of a `#[ToolSetting]`, only `description` is operator copy and translates;
// `label` and `options` never do. The README's "What not to translate" table
// gives the reason.
#[ToolSetting(
    key: 'greeting',
    label: 'Greeting',
    type: 'text',
    description: 'Prefix placed before the returned text.',
    default: 'Hello',
)]
#[ToolSetting(
    key: 'tone',
    label: 'Tone',
    type: 'select',
    description: 'How the greeting is phrased.',
    default: 'casual',
    options: [
        'formal'   => 'Formal',
        'casual'   => 'Casual',
        'terse'    => 'Terse',
    ],
)]
final class CompanionTool extends AbstractTool
{
    /**
     * @param array<string, mixed> $arguments
     */
    public function execute(
        array $arguments,
        int $agentId,
        ?int $taskId = null,
        ?PrincipalContext $context = null,
    ): ToolResult {
        return ToolResult::ok(
            content: 'companion ok',
            data: ['status' => 'ok'],
        );
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function describeAction(array $arguments): string
    {
        return 'Companion: bundle-skill demo';
    }
}
