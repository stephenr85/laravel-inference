<?php

namespace Splicewire\Beam\Agent\Contracts;

/**
 * Opt-in capability: the agent wraps its turn in middleware. Mirrors `laravel/ai`'s `HasMiddleware`.
 * The array is engine-agnostic middleware descriptors; an adapter resolves them against its own pipeline.
 */
interface HasMiddleware
{
    /**
     * @return array<int, mixed>
     */
    public function middleware(): array;
}
