<?php

namespace Splicewire\BeamInference;

/**
 * A single structured-generation call, domain-agnostic. The caller assembles the LLM target (per-call
 * `provider`/`model`), the named JSON schema, the rules-only system prompt, and the structured user
 * prompt; a host {@see StructuredLlm} turns it into one transport call. This is the consumer->inference
 * seam (ADR-0125, amends ADR-0023). Kept Prism-free: `schema` is a plain {@see RawSchema}.
 */
class GenerationRequest
{
    /**
     * @param  array<string, mixed>  $providerOptions
     * @param  array<string, mixed>  $clientOptions
     */
    public function __construct(
        public string $provider,
        public string $model,
        public RawSchema $schema,
        public string $systemPrompt,
        public string $prompt,
        public array $providerOptions = ['schema' => ['strict' => true]],
        public array $clientOptions = [],
    ) {}
}
