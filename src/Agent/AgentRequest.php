<?php

namespace Rushing\Inference\Agent;

use Rushing\Inference\GenerationRequest;

/**
 * A single agent turn, engine-agnostic — the Agent-seam sibling of {@see GenerationRequest}.
 * The caller (or the {@see AgentDefinition} normalizer) assembles the LLM target (`provider`/`model`),
 * the system `instructions`, the user `prompt`, prior `messages`, opt-in `tools`/`middleware`, an optional
 * output `schema`, and the turn-loop knobs (`maxSteps`/`temperature`); a host {@see Contracts\AgentRunner}
 * turns it into one engine call. Kept Prism-free and `laravel/ai`-free: plain arrays at the boundary.
 */
class AgentRequest
{
    /**
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<int, mixed>  $tools
     * @param  array<int, mixed>  $middleware
     * @param  array<string, mixed>|null  $schema
     * @param  array<string, mixed>  $providerOptions
     */
    public function __construct(
        public string $provider,
        public string $model,
        public string $instructions,
        public string $prompt = '',
        public array $messages = [],
        public array $tools = [],
        public array $middleware = [],
        public ?array $schema = null,
        public ?int $maxSteps = null,
        public ?float $temperature = null,
        public array $providerOptions = [],
    ) {}
}
