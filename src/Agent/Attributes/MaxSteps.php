<?php

namespace Rushing\Inference\Agent\Attributes;

use Attribute;
use Rushing\Inference\Agent\AgentDefinition;

/**
 * Class-declared agents cap their turn-loop with `#[MaxSteps(10)]`. Mirrors `laravel/ai`'s `#[MaxSteps(...)]`.
 * {@see AgentDefinition} reads it when normalizing.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class MaxSteps
{
    public function __construct(
        public int $maxSteps,
    ) {}
}
