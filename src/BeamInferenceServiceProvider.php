<?php

namespace Splicewire\Beam\Inference;

use Illuminate\Support\ServiceProvider;
use Splicewire\Beam\Inference\Prism\PrismStructuredLlm;

/**
 * Wires the public inference substrate. It binds the {@see StructuredLlm} port to the vanilla,
 * unmetered {@see PrismStructuredLlm} reference binding — the shape a bare satellite gets out of the
 * box. The Splicewire platform rebinds a metered implementation over the same port from its own
 * (private, app-local) service provider, which — registering later — wins. The metering moat therefore
 * never ships in this package (ADR-0125).
 */
class BeamInferenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StructuredLlm::class, PrismStructuredLlm::class);
    }
}
