<?php

namespace Splicewire\Beam\Inference\Prism;

use Prism\Prism\Enums\StructuredMode;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\RawSchema as PrismRawSchema;
use Splicewire\Beam\Inference\GenerationRequest;
use Splicewire\Beam\Inference\GenerationResult;
use Splicewire\Beam\Inference\RawSchema;
use Splicewire\Beam\Inference\StructuredLlm;
use Splicewire\Beam\Inference\Usage;

/**
 * The vanilla, unmetered reference binding shipped with the public substrate (ADR-0125): one plain
 * `Prism::structured()` call from a {@see GenerationRequest}, no conduits, no charge-through, no token
 * metering. A bare satellite resolves the {@see StructuredLlm} port to this; the Splicewire platform
 * rebinds a metered implementation over the same port app-side, so the metering moat never enters this
 * package.
 *
 * The caller carries a pre-built JSON Schema array ({@see RawSchema}); we wrap
 * it in Prism's {@see PrismRawSchema} so schema authority stays the caller's projection, not a
 * hand-built Prism schema.
 */
final class PrismStructuredLlm implements StructuredLlm
{
    public function generate(GenerationRequest $request): GenerationResult
    {
        $pending = Prism::structured()
            ->using($request->provider, $request->model)
            ->withSchema(new PrismRawSchema($request->schema->name(), $request->schema->toArray()))
            ->usingStructuredMode(StructuredMode::Structured);

        if ($request->systemPrompt !== '') {
            $pending->withSystemPrompt($request->systemPrompt);
        }

        $pending->withPrompt($request->prompt);

        $response = $pending->asStructured();

        return new GenerationResult(
            structured: is_array($response->structured ?? null) ? $response->structured : [],
            usage: new Usage(
                promptTokens: $response->usage->promptTokens ?? 0,
                completionTokens: $response->usage->completionTokens ?? 0,
            ),
        );
    }
}
