<?php

namespace Splicewire\Beam\Inference\Tests\Agent\Fakes;

use Splicewire\Beam\Agent\Contracts\Agent;
use Stringable;

/**
 * The minimum an agent can be: the {@see Agent} marker and nothing else, with a `Stringable` instructions
 * body and no target attributes. Proves the normalizer degrades cleanly when no capability is opted into.
 */
class BareAgent implements Agent
{
    public function instructions(): string|Stringable
    {
        return new class implements Stringable
        {
            public function __toString(): string
            {
                return 'bare';
            }
        };
    }
}
