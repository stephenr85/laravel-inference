<?php

namespace Splicewire\Beam\Agent;

use ReflectionClass;
use Splicewire\Beam\Agent\Attributes\MaxSteps;
use Splicewire\Beam\Agent\Attributes\Model;
use Splicewire\Beam\Agent\Attributes\Provider;
use Splicewire\Beam\Agent\Attributes\Temperature;
use Splicewire\Beam\Agent\Contracts\Agent;
use Splicewire\Beam\Agent\Contracts\CanActAsTool;
use Splicewire\Beam\Agent\Contracts\Conversational;
use Splicewire\Beam\Agent\Contracts\HasMiddleware;
use Splicewire\Beam\Agent\Contracts\HasProviderOptions;
use Splicewire\Beam\Agent\Contracts\HasStructuredOutput;
use Splicewire\Beam\Agent\Contracts\HasTools;
use Splicewire\Beam\Agent\Contracts\Promptable;

/**
 * The **normalizer stub** of the Agent seam (PRD §3): unifies the two definition sources into one
 * {@see AgentRequest} for an {@see Contracts\AgentRunner}.
 *
 *  1. A **class-declared agent** — an {@see Agent} marker instance whose LLM target comes from
 *     `#[Provider]`/`#[Model]`/`#[MaxSteps]`/`#[Temperature]` class attributes, and whose opt-in
 *     capabilities ({@see Conversational}, {@see HasStructuredOutput}, {@see HasTools}, …) are read only
 *     when the instance implements them.
 *  2. A **DB-configured agent row** — a plain associative array (the `Agent` model's attributes) with the
 *     same keys, so the DB and the class converge on the same envelope.
 *
 * Deliberately a *stub*: it fixes the shape and the unification rule; the engine (turn-loop, tool wiring,
 * federated model selection) stays private in Tower behind the runner port. Being a {@see Promptable}, a
 * definition is itself runnable — `$runner->prompt($definition->toAgentRequest())`.
 */
class AgentDefinition implements Promptable
{
    private function __construct(
        private AgentRequest $request,
    ) {}

    /**
     * Normalize a class-declared agent (marker + attributes + opt-in capabilities) into a definition.
     */
    public static function fromAgent(Agent $agent, string $prompt = ''): self
    {
        $reflection = new ReflectionClass($agent);

        $provider = self::attributeValue($reflection, Provider::class, 'provider') ?? '';
        $model = self::attributeValue($reflection, Model::class, 'model') ?? '';
        $maxSteps = self::attributeValue($reflection, MaxSteps::class, 'maxSteps');
        $temperature = self::attributeValue($reflection, Temperature::class, 'temperature');

        return new self(new AgentRequest(
            provider: (string) $provider,
            model: (string) $model,
            instructions: (string) $agent->instructions(),
            prompt: $prompt,
            messages: $agent instanceof Conversational ? self::materialize($agent->messages()) : [],
            tools: $agent instanceof HasTools ? self::materialize($agent->tools()) : [],
            middleware: $agent instanceof HasMiddleware ? array_values($agent->middleware()) : [],
            schema: $agent instanceof HasStructuredOutput ? $agent->schema() : null,
            maxSteps: $maxSteps !== null ? (int) $maxSteps : null,
            temperature: $temperature !== null ? (float) $temperature : null,
            providerOptions: $agent instanceof HasProviderOptions
                ? $agent->providerOptions((string) $provider)
                : [],
        ));
    }

    /**
     * Normalize a DB-configured agent row (the `Agent` model's attributes) into a definition. Same keys
     * as the class path, so DB and class converge on one envelope.
     *
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row, string $prompt = ''): self
    {
        return new self(new AgentRequest(
            provider: (string) ($row['provider'] ?? ''),
            model: (string) ($row['model'] ?? ''),
            instructions: (string) ($row['instructions'] ?? ''),
            prompt: $prompt !== '' ? $prompt : (string) ($row['prompt'] ?? ''),
            messages: array_values($row['messages'] ?? []),
            tools: array_values($row['tools'] ?? []),
            middleware: array_values($row['middleware'] ?? []),
            schema: $row['schema'] ?? null,
            maxSteps: isset($row['max_steps']) ? (int) $row['max_steps'] : null,
            temperature: isset($row['temperature']) ? (float) $row['temperature'] : null,
            providerOptions: $row['provider_options'] ?? [],
        ));
    }

    public function toAgentRequest(): AgentRequest
    {
        return $this->request;
    }

    /**
     * Whether a class-declared agent (or DB row wrapped as one) can be exposed as a sub-agent tool.
     */
    public static function actsAsTool(Agent $agent): bool
    {
        return $agent instanceof CanActAsTool;
    }

    /**
     * @param  ReflectionClass<object>  $reflection
     * @param  class-string  $attribute
     */
    private static function attributeValue(ReflectionClass $reflection, string $attribute, string $property): mixed
    {
        $attributes = $reflection->getAttributes($attribute);

        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance()->{$property};
    }

    /**
     * @param  iterable<int, mixed>  $items
     * @return array<int, mixed>
     */
    private static function materialize(iterable $items): array
    {
        return is_array($items) ? array_values($items) : iterator_to_array($items, false);
    }
}
