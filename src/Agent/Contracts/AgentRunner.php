<?php

namespace Splicewire\Beam\Agent\Contracts;

use Splicewire\Beam\Agent\AgentRequest;
use Splicewire\Beam\Agent\AgentResult;
use Splicewire\Beam\Inference\StructuredLlm;

/**
 * The **runner port** of the Agent seam — the Agent-seam sibling of {@see StructuredLlm}.
 * A consumer depends on this contract, never on the engine: a host binds a Prism-backed implementation
 * (Tower, private) into the container; a future `laravel/ai`-backed implementation plugs in behind the
 * same port without a fork. Method names mirror `laravel/ai`'s Agent runner (`prompt`/`stream`/`queue`).
 * Array-in/array-out at the boundary — an {@see AgentRequest} in, an {@see AgentResult} out.
 */
interface AgentRunner
{
    /**
     * Run one agent turn synchronously and return its result.
     */
    public function prompt(AgentRequest $request): AgentResult;

    /**
     * Run one agent turn, streaming intermediate output. Returns an iterable of engine-agnostic chunks
     * (the adapter defines the chunk shape); the shape stays open so a Prism or `laravel/ai` adapter can
     * both satisfy it.
     *
     * @return iterable<int, mixed>
     */
    public function stream(AgentRequest $request): iterable;

    /**
     * Enqueue one agent turn for background execution. Returns an engine-agnostic handle (e.g. a
     * conversation/job id) the caller can later resolve — the seam does not prescribe the handle type.
     */
    public function queue(AgentRequest $request): mixed;
}
