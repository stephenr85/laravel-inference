<?php

namespace Rushing\Inference\Agent\Contracts;

use Rushing\Inference\RawSchema;

/**
 * Opt-in capability: the agent constrains its output to a JSON schema. Mirrors `laravel/ai`'s
 * `HasStructuredOutput`. Kept Prism-free / `laravel/ai`-free: the schema is a plain array (the same
 * carrier idiom as {@see RawSchema}), not a `Prism\Schema` or a `JsonSchema`.
 */
interface HasStructuredOutput
{
    /**
     * @return array<string, mixed>
     */
    public function schema(): array;
}
