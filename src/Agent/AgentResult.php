<?php

namespace Rushing\Inference\Agent;

use Rushing\Inference\GenerationResult;

/**
 * The result of one agent turn — the Agent-seam sibling of {@see GenerationResult}.
 * Carries the model's `text`, its `structured` payload when the agent {@see Contracts\HasStructuredOutput}
 * (else empty), any `steps` the turn-loop took (tool calls / intermediate turns), and the token `usage`.
 * The caller hydrates `structured` into its own types. Prism-free: `usage` is the seam's own {@see Usage}.
 */
class AgentResult
{
    /**
     * @param  array<string, mixed>  $structured
     * @param  array<int, mixed>  $steps
     */
    public function __construct(
        public string $text = '',
        public array $structured = [],
        public array $steps = [],
        public Usage $usage = new Usage,
    ) {}
}
