<?php

namespace Rushing\Inference\Agent\Attributes;

use Attribute;
use Rushing\Inference\Agent\AgentDefinition;

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
