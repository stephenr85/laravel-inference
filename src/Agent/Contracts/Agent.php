<?php

namespace Rushing\Inference\Agent\Contracts;

use Stringable;

/**
 * The open, engine-agnostic **Agent seam** (PRD §3, ADR issue 06 — sibling of the {@see
 * \Rushing\Inference\StructuredLlm} port). This marker is the convergence surface: any agent —
 * a DB-configured `Agent` row *or* a class-declared agent (marker + `#[Provider]`/`#[Model]`/… attributes)
 * — satisfies it, and both a Prism adapter (Tower, private) and a future `laravel/ai` adapter plug in
 * behind the same {@see AgentRunner} port. Method name mirrors `laravel/ai`'s Agent contract.
 *
 * Capabilities are **opt-in** and segregated (ISP): an agent implements {@see Conversational},
 * {@see HasStructuredOutput}, {@see HasTools}, {@see HasMiddleware}, {@see HasProviderOptions},
 * {@see CanActAsTool} only for the shapes it actually carries — never one fat interface.
 */
interface Agent
{
    /**
     * The agent's system instructions (the rules-only system prompt). Mirrors `laravel/ai`'s
     * `Agent::instructions()`; a `Stringable` is accepted so instructions can be lazily rendered.
     */
    public function instructions(): string|Stringable;
}
