<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use Spora\Services\PrincipalContext;
use Spora\Tools\AbstractTool;
use Spora\Tools\Attributes\Tool;
use Spora\Tools\ValueObjects\ToolResult;

/**
 * Fork-and-go example that ships a tool + skill pair.
 *
 * The bundled skill lives at `skills/companion-skill/SKILL.md` and is
 * loaded by Spora's SkillScanner at boot. `#[Tool(recommendsSkills: ...)]`
 * shipped in spora-core v0.29.0 (PR spora-core#269) and is declared below,
 * so the operator UI's "Enable skill" affordance picks the skill up
 * automatically.
 *
 * The slug MUST match the skill directory name. Renaming one without the
 * other fails core's strict-mode check with `TOOLS_RECOMMENDS_SKILLS_MISSING`;
 * `tests/Unit/BundledSkillTest.php` enforces the pairing at build time.
 */
#[Tool(
    name: 'companion',
    description: 'Demo tool that bundles a skill — fork-and-go example.',
    recommendsSkills: ['companion-skill'],
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
