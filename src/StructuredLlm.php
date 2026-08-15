<?php

namespace Rushing\Inference;

/**
 * The single structured-generation seam (ADR-0125, amends ADR-0023 which originally placed this seam
 * inside `laravel-composition-engine`). A consumer stays Prism-free: it depends on this contract, never
 * on `prism-php/prism` directly. A host binds a Prism-backed implementation (or any other LLM transport)
 * into the container; the platform binds a metered implementation over the same port, a bare satellite
 * the vanilla {@see Prism\PrismStructuredLlm}. Array-in/array-out at the boundary — a
 * {@see GenerationRequest} in, a {@see GenerationResult} out.
 */
interface StructuredLlm
{
    public function generate(GenerationRequest $request): GenerationResult;
}
