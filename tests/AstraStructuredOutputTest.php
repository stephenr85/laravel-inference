<?php

namespace Rushing\Inference\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Prism\Prism\Enums\StructuredMode;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Facades\Prism;
use Prism\Prism\PrismServiceProvider;
use Prism\Prism\Schema\RawSchema;
use Rushing\Inference\InferenceServiceProvider;

class AstraStructuredOutputTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [PrismServiceProvider::class, InferenceServiceProvider::class];
    }

    #[DataProvider('models')]
    public function test_exact_astra_uses_native_schema_and_per_call_credentials(string $model, StructuredMode $mode, string $name = 'result'): void
    {
        Http::preventStrayRequests();
        Http::fake(['example.test/*' => Http::response(['id' => 'resp_fixture', 'model' => 'gpt-6-astra', 'status' => 'completed', 'output' => [['type' => 'message', 'status' => 'completed', 'role' => 'assistant', 'content' => [['type' => 'output_text', 'text' => '{"answer":42}']]]], 'usage' => ['input_tokens' => 11, 'output_tokens' => 7, 'total_tokens' => 18]])]);
        $schema = ['type' => 'object', 'properties' => ['answer' => ['type' => 'integer']], 'required' => ['answer'], 'additionalProperties' => false];
        $response = Prism::structured()->using('openai', $model, ['api_key' => 'fixture-key', 'url' => 'https://example.test/v1', 'organization' => 'fixture-org', 'project' => 'fixture-project'])->withSchema(new RawSchema($name, $schema))->usingStructuredMode($mode)->withProviderOptions(['schema' => ['strict' => true]])->withPrompt('Return42')->asStructured();
        self::assertSame(['answer' => 42], $response->structured);
        self::assertSame(11, $response->usage->promptTokens);
        Http::assertSent(fn ($request) => $request->url() === 'https://example.test/v1/responses' && $request->hasHeader('Authorization', 'Bearer fixture-key') && $request['model'] === $model && $request['text']['format'] === ['type' => 'json_schema', 'name' => ($name === 'result' ? $name : 'schema_'.substr(hash('sha256', $name), 0, 56)), 'schema' => $schema, 'strict' => true]);
    }

    public static function models(): array
    {
        return [['gpt-6-astra', StructuredMode::Structured], ['gpt-6-astra', StructuredMode::Auto], ['gpt-5.4', StructuredMode::Structured], ['gpt-6-astra', StructuredMode::Structured, 'music/intent']];
    }

    public function test_unknown_model_still_refuses_before_http(): void
    {
        Http::preventStrayRequests();
        Http::fake();
        try {
            Prism::structured()->using('openai', 'gpt-6-imaginary')->withSchema(new RawSchema('result', ['type' => 'object']))->usingStructuredMode(StructuredMode::Structured)->withPrompt('x')->asStructured();
            self::fail('Expected refusal');
        } catch (PrismException $e) {
            self::assertStringContainsString('does not support structured mode', $e->getMessage());
        }
        Http::assertNothingSent();
    }
}
