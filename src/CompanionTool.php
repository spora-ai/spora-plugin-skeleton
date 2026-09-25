<?php

declare(strict_types=1);

namespace Spora\Plugins\Skeleton;

use Spora\Tools\AbstractTool;
use Spora\Tools\Attributes\Tool;
use Spora\Tools\ValueObjects\ToolResult;

/**
 * Fork-and-go example demonstrating the `recommendsSkills` attribute parameter.
 *
 * The skill slug listed below is bundled by this plugin via
 * {@see SkeletonPlugin::skillPaths()}. The runtime validator in spora-core
 * raises `TOOLS_RECOMMENDS_SKILLS_MISSING` on `GET /api/v1/tools` when a
 * declared slug is not on disk — the test in `tests/Unit/CompanionToolValidationTest.php`
 * catches that regression before publish.
 */
#[Tool(
    name: 'companion',
    description: 'Demo tool that bundles a skill — fork-and-go example of #[Tool(recommendsSkills: ...)].',
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
        ?int $userId = null,
        ?int $taskId = null,
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
