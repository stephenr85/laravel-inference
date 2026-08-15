<?php

namespace Rushing\Inference\Agent\Contracts;

/**
 * Opt-in capability: the agent can be exposed to another agent as a callable tool (a sub-agent). Mirrors
 * `laravel/ai`'s `CanActAsTool` and matches this estate's existing **Agent-as-Tool** conduit
 * (`internal.agent`) — once Assistant→Agent, "Agent-as-Tool" literally means "expose an Agent as a tool".
 */
interface CanActAsTool
{
    public function name(): string;

    public function description(): string;
}
