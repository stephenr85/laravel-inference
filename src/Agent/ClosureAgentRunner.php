<?php

namespace Rushing\Inference\Agent;

use Closure;
use Rushing\Inference\Agent\Contracts\AgentRunner;
use Rushing\Inference\ClosureStructuredLlm;

/**
 * An {@see AgentRunner} backed by closures — the test/seed default and the simplest host binding, mirroring
 * {@see ClosureStructuredLlm}. A host that wires Prism (Tower) wraps its
 * turn-loop machinery in the `prompt` closure here, keeping the engine dependency entirely on the host
 * side. Only `prompt` is required; `stream`/`queue` fall back to it unless closures are supplied.
 */
class ClosureAgentRunner implements AgentRunner
{
    private Closure $prompt;

    private ?Closure $stream;

    private ?Closure $queue;

    /**
     * @param  callable(AgentRequest): AgentResult  $prompt
     * @param  (callable(AgentRequest): iterable<int, mixed>)|null  $stream
     * @param  (callable(AgentRequest): mixed)|null  $queue
     */
    public function __construct(callable $prompt, ?callable $stream = null, ?callable $queue = null)
    {
        $this->prompt = $prompt(...);
        $this->stream = $stream !== null ? $stream(...) : null;
        $this->queue = $queue !== null ? $queue(...) : null;
    }

    public function prompt(AgentRequest $request): AgentResult
    {
        return ($this->prompt)($request);
    }

    public function stream(AgentRequest $request): iterable
    {
        if ($this->stream !== null) {
            return ($this->stream)($request);
        }

        return [$this->prompt($request)];
    }

    public function queue(AgentRequest $request): mixed
    {
        if ($this->queue !== null) {
            return ($this->queue)($request);
        }

        return $this->prompt($request);
    }
}
