<?php

namespace Splicewire\Beam\Agent\Attributes;

use Attribute;
use Splicewire\Beam\Agent\AgentDefinition;

/**
 * Class-declared agents pin their model with `#[Model('claude-sonnet-5')]`. Mirrors `laravel/ai`'s
 * `#[Model(...)]`. {@see AgentDefinition} reads it when normalizing.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Model
{
    public function __construct(
        public string $model,
    ) {}
}
