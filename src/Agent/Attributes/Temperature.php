<?php

namespace Rushing\Inference\Agent\Attributes;

use Attribute;
use Rushing\Inference\Agent\AgentDefinition;

/**
 * Class-declared agents pin sampling temperature with `#[Temperature(0.7)]`. Mirrors `laravel/ai`'s
 * `#[Temperature(...)]`. {@see AgentDefinition} reads it when normalizing.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Temperature
{
    public function __construct(
        public float $temperature,
    ) {}
}
