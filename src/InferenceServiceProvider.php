<?php

namespace Rushing\Inference;

use Illuminate\Support\ServiceProvider;
use Prism\Prism\PrismManager;
use Rushing\Inference\Prism\OpenAI;
use Rushing\Inference\Prism\PrismStructuredLlm;

/**
 * Wires the public inference substrate. It binds the {@see StructuredLlm} port to the vanilla,
 * unmetered {@see PrismStructuredLlm} reference binding — the shape a bare satellite gets out of the
 * box. A platform rebinds a metered implementation over the same port from its own
 * (private, app-local) service provider, which — registering later — wins. The metering moat therefore
 * never ships in this package (ADR-0125).
 */
class InferenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StructuredLlm::class, PrismStructuredLlm::class);
        $this->app->afterResolving(PrismManager::class, static function ($manager): void {
            $manager->extend('openai', static fn ($app, array $config) => new OpenAI(
                apiKey: $config['api_key'] ?? '', url: $config['url'],
                organization: $config['organization'] ?? null, project: $config['project'] ?? null,
            ));
        });
    }
}
