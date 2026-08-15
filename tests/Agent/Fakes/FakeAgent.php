<?php

namespace Rushing\Inference\Tests\Agent\Fakes;

use Rushing\Inference\Agent\AgentDefinition;
use Rushing\Inference\Agent\Attributes\MaxSteps;
use Rushing\Inference\Agent\Attributes\Model;
use Rushing\Inference\Agent\Attributes\Provider;
use Rushing\Inference\Agent\Attributes\Temperature;
use Rushing\Inference\Agent\Contracts\Agent;
use Rushing\Inference\Agent\Contracts\CanActAsTool;
use Rushing\Inference\Agent\Contracts\Conversational;
use Rushing\Inference\Agent\Contracts\HasMiddleware;
use Rushing\Inference\Agent\Contracts\HasProviderOptions;
use Rushing\Inference\Agent\Contracts\HasStructuredOutput;
use Rushing\Inference\Agent\Contracts\HasTools;

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
