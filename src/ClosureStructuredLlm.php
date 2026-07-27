<?php

namespace Splicewire\Beam\Inference;

use Closure;

/**
 * A {@see StructuredLlm} backed by a closure — the test/seed default and the simplest host binding. A
 * host that wires Prism wraps its `Prism::structured()` call in a closure here, keeping the Prism
 * dependency entirely on the host side.
 */
class ClosureStructuredLlm implements StructuredLlm
{
    private Closure $handler;

    /**
     * @param  callable(GenerationRequest): GenerationResult  $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler(...);
    }

    public function generate(GenerationRequest $request): GenerationResult
    {
        return ($this->handler)($request);
    }
}
