<?php

namespace Splicewire\Beam\Agent;

/**
 * Token usage of one agent turn. A Prism-free value object so {@see AgentResult} carries usage without
 * depending on the engine; an adapter maps its transport's own usage into this. The Agent-seam sibling of
 * {@see \Splicewire\Beam\Inference\Usage} — kept as its own type so the two seams don't cross-depend.
 */
class Usage
{
    public function __construct(
        public int $promptTokens = 0,
        public int $completionTokens = 0,
    ) {}
}
