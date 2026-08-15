<?php

namespace Rushing\Inference\Agent\Contracts;

/**
 * Opt-in capability: the agent exposes callable tools to the model. Mirrors `laravel/ai`'s `HasTools`.
 * Each yielded item is a tool definition — including, per the existing "Agent-as-Tool" conduit, another
 * agent that {@see CanActAsTool} (expose an Agent as a tool). Kept engine-agnostic: an adapter maps these
 * into whatever its transport needs.
 */
interface HasTools
{
    /**
     * @return iterable<int, mixed>
     */
    public function tools(): iterable;
}
