<?php

namespace Splicewire\Beam\Agent\Contracts;

/**
 * Opt-in capability: the agent carries prior conversation turns. Mirrors `laravel/ai`'s `Conversational`
 * interface. Each yielded item is a wire message shape (a `['role' => 'user'|'assistant'|'system', ...]`
 * array — the `'assistant'` role is the external LLM protocol literal, not this estate's Agent entity).
 */
interface Conversational
{
    /**
     * @return iterable<int, array<string, mixed>>
     */
    public function messages(): iterable;
}
