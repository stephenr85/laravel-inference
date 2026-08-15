<?php

namespace Rushing\Inference\Agent\Attributes;

use Attribute;
use Rushing\Inference\Agent\AgentDefinition;

/**
 * Class-declared agents pin their LLM provider with `#[Provider('anthropic')]`. Mirrors `laravel/ai`'s
 * `#[Provider(Lab::Anthropic)]`, kept engine-agnostic: a plain provider-id string, not a `Lab` enum.
 * {@see AgentDefinition} reads it when normalizing a class into an `AgentRequest`.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Provider
{
    public function __construct(
        public string $provider,
    ) {}
}
