<?php

namespace Splicewire\Beam\Inference\Tests\Agent;

use PHPUnit\Framework\TestCase;
use Splicewire\Beam\Agent\AgentDefinition;
use Splicewire\Beam\Agent\AgentRequest;
use Splicewire\Beam\Agent\AgentResult;
use Splicewire\Beam\Agent\ClosureAgentRunner;
use Splicewire\Beam\Agent\Contracts\Agent;
use Splicewire\Beam\Agent\Contracts\AgentRunner;
use Splicewire\Beam\Agent\Contracts\CanActAsTool;
use Splicewire\Beam\Agent\Contracts\Promptable;
use Splicewire\Beam\Agent\Usage;
use Splicewire\Beam\Inference\Tests\Agent\Fakes\BareAgent;
use Splicewire\Beam\Inference\Tests\Agent\Fakes\FakeAgent;

/**
 * Proves the Agent interface family is coherent end-to-end without any engine: a fake in-package agent
 * normalizes into an {@see AgentRequest} through {@see AgentDefinition}, and a fake {@see ClosureAgentRunner}
 * turns it into an {@see AgentResult}. This is the acceptance test AGT-01 requires.
 */
class AgentSeamContractTest extends TestCase
{
    public function test_class_declared_agent_normalizes_every_capability_and_attribute(): void
    {
        $request = AgentDefinition::fromAgent(new FakeAgent, prompt: 'go')->toAgentRequest();

        // Target comes from #[Provider]/#[Model]/#[MaxSteps]/#[Temperature].
        $this->assertSame('anthropic', $request->provider);
        $this->assertSame('claude-sonnet-5', $request->model);
        $this->assertSame(7, $request->maxSteps);
        $this->assertSame(0.4, $request->temperature);

        // instructions() + prompt.
        $this->assertSame('You are a fake test agent.', $request->instructions);
        $this->assertSame('go', $request->prompt);

        // Opt-in capabilities are read only when implemented.
        $this->assertCount(2, $request->messages);
        $this->assertSame('user', $request->messages[0]['role']);
        // The 'assistant' role is the external LLM wire literal, deliberately not renamed.
        $this->assertSame('assistant', $request->messages[1]['role']);
        $this->assertSame(['clock'], $request->tools);
        $this->assertSame(['log'], $request->middleware);
        $this->assertSame('object', $request->schema['type']);
        $this->assertTrue($request->providerOptions['schema']['strict']);
        $this->assertSame('anthropic', $request->providerOptions['provider']);
    }

    public function test_bare_marker_agent_degrades_cleanly_with_no_capabilities(): void
    {
        $request = AgentDefinition::fromAgent(new BareAgent)->toAgentRequest();

        $this->assertSame('bare', $request->instructions);
        $this->assertSame('', $request->provider);
        $this->assertSame('', $request->model);
        $this->assertNull($request->maxSteps);
        $this->assertNull($request->temperature);
        $this->assertSame([], $request->messages);
        $this->assertSame([], $request->tools);
        $this->assertSame([], $request->middleware);
        $this->assertNull($request->schema);
        $this->assertSame([], $request->providerOptions);
    }

    public function test_db_configured_row_normalizes_to_the_same_envelope(): void
    {
        $request = AgentDefinition::fromArray([
            'provider' => 'openai',
            'model' => 'gpt-x',
            'instructions' => 'row instructions',
            'messages' => [['role' => 'user', 'content' => 'hi']],
            'tools' => ['search'],
            'max_steps' => 3,
            'temperature' => 0.2,
        ], prompt: 'ask')->toAgentRequest();

        $this->assertInstanceOf(AgentRequest::class, $request);
        $this->assertSame('openai', $request->provider);
        $this->assertSame('gpt-x', $request->model);
        $this->assertSame('row instructions', $request->instructions);
        $this->assertSame('ask', $request->prompt);
        $this->assertSame(['search'], $request->tools);
        $this->assertSame(3, $request->maxSteps);
        $this->assertSame(0.2, $request->temperature);
    }

    public function test_fake_runner_turns_a_request_into_a_result_through_the_port(): void
    {
        $runner = new ClosureAgentRunner(function (AgentRequest $request): AgentResult {
            return new AgentResult(
                text: 'ran '.$request->model,
                structured: ['score' => 9],
                steps: ['tool:clock'],
                usage: new Usage(promptTokens: 11, completionTokens: 3),
            );
        });

        // A definition is Promptable; the runner consumes what it emits.
        $definition = AgentDefinition::fromAgent(new FakeAgent, prompt: 'go');
        $this->assertInstanceOf(Promptable::class, $definition);

        $result = $runner->prompt($definition->toAgentRequest());

        $this->assertInstanceOf(AgentRunner::class, $runner);
        $this->assertSame('ran claude-sonnet-5', $result->text);
        $this->assertSame(['score' => 9], $result->structured);
        $this->assertSame(['tool:clock'], $result->steps);
        $this->assertSame(11, $result->usage->promptTokens);
        $this->assertSame(3, $result->usage->completionTokens);
    }

    public function test_stream_and_queue_fall_back_to_prompt_when_no_closure_given(): void
    {
        $runner = new ClosureAgentRunner(fn (AgentRequest $r): AgentResult => new AgentResult(text: 'once'));

        $request = AgentDefinition::fromAgent(new BareAgent)->toAgentRequest();

        $streamed = iterator_to_array((function () use ($runner, $request) {
            yield from $runner->stream($request);
        })());
        $this->assertCount(1, $streamed);
        $this->assertSame('once', $streamed[0]->text);

        $queued = $runner->queue($request);
        $this->assertInstanceOf(AgentResult::class, $queued);
        $this->assertSame('once', $queued->text);
    }

    public function test_agent_that_acts_as_tool_is_detectable(): void
    {
        $this->assertTrue(AgentDefinition::actsAsTool(new FakeAgent));
        $this->assertFalse(AgentDefinition::actsAsTool(new BareAgent));

        // The marker and the sub-agent capability are distinct interfaces (ISP).
        $this->assertInstanceOf(Agent::class, new FakeAgent);
        $this->assertInstanceOf(CanActAsTool::class, new FakeAgent);
    }
}
