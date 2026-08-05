<?php

namespace Splicewire\Beam\Inference\Tests\Agent\Fakes;

use Splicewire\Beam\Agent\AgentDefinition;
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

/**
 * An in-package fake that opts into every capability of the Agent seam at once, proving the segregated
 * interface family composes coherently and that {@see AgentDefinition} reads each
 * opt-in only when present. Not shipped — a test fixture.
 */
#[Provider('anthropic')]
#[Model('claude-sonnet-5')]
#[MaxSteps(7)]
#[Temperature(0.4)]
class FakeAgent implements Agent, CanActAsTool, Conversational, HasMiddleware, HasProviderOptions, HasStructuredOutput, HasTools
{
    public function instructions(): string
    {
        return 'You are a fake test agent.';
    }

    public function messages(): iterable
    {
        yield ['role' => 'user', 'content' => 'hi'];
        yield ['role' => 'assistant', 'content' => 'hello'];
    }

    public function schema(): array
    {
        return ['type' => 'object', 'properties' => ['score' => ['type' => 'integer']]];
    }

    public function tools(): iterable
    {
        return ['clock'];
    }

    public function middleware(): array
    {
        return ['log'];
    }

    public function providerOptions(string $provider): array
    {
        return ['schema' => ['strict' => true], 'provider' => $provider];
    }

    public function name(): string
    {
        return 'fake-agent';
    }

    public function description(): string
    {
        return 'A fake agent that can act as a sub-agent tool.';
    }
}
