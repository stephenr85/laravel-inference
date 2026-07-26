<?php

namespace Splicewire\BeamInference;

/**
 * The result of a structured-generation call: the raw structured payload the model returned (matching
 * the request schema) plus the token usage. The caller hydrates the structured array into its own types
 * (a Data class, Block items, ...). Prism-free: `usage` is the port's own {@see Usage}.
 */
class GenerationResult
{
    /**
     * @param  array<string, mixed>  $structured
     */
    public function __construct(
        public array $structured,
        public Usage $usage = new Usage,
    ) {}
}
