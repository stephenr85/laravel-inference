<?php

namespace Rushing\Inference\Agent\Contracts;

use Rushing\Inference\Agent\AgentDefinition;
use Rushing\Inference\Agent\AgentRequest;

/**
 * The `Promptable`-equivalent of the Agent seam: something that can normalize itself into a single
 * {@see AgentRequest} for the {@see AgentRunner}. Mirrors `laravel/ai`'s promptable shape (an agent is
 * runnable), kept as its own interface so a definition source that is *not* an {@see Agent} class (a
 * DB-configured row wrapped by {@see AgentDefinition}) can also be promptable.
 */
interface Promptable
{
    /**
     * Reduce this promptable into the one envelope the runner consumes.
     */
    public function toAgentRequest(): AgentRequest;
}
