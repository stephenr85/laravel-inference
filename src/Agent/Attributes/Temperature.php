<?php

namespace Splicewire\Beam\Agent\Attributes;

use Attribute;
use Splicewire\Beam\Agent\AgentDefinition;

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
