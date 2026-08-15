<?php

namespace Rushing\Inference\Agent\Contracts;

use Rushing\Inference\GenerationRequest;

/**
 * Opt-in capability: the agent carries per-provider transport options. Mirrors `laravel/ai`'s
 * `HasProviderOptions`. Kept engine-agnostic: `$provider` is a plain string id (not a `Lab` enum), and
 * the returned options are a plain array — the same idiom as {@see GenerationRequest}'s
 * `$providerOptions`.
 */
interface HasProviderOptions
{
    /**
     * @return array<string, mixed>
     */
    public function providerOptions(string $provider): array;
}
