<?php

namespace Splicewire\Beam\Inference;

/**
 * A named, pre-built JSON Schema array (from a Data->JSON-Schema generator) carried to the LLM seam.
 * The port stays Prism-free: this is a plain schema carrier, not a `Prism\Schema`. A host's
 * {@see StructuredLlm} adapter wraps it into whatever its transport needs (e.g. Prism's
 * structured-schema contract).
 */
class RawSchema
{
    /**
     * @param  array<string, mixed>  $schema
     */
    public function __construct(
        private string $name,
        private array $schema,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->schema;
    }
}
