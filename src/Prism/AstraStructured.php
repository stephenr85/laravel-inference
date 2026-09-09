<?php

namespace Rushing\Inference\Prism;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Prism\Prism\Providers\OpenAI\Handlers\Structured;
use Prism\Prism\Structured\Request;

/** Native Responses transport/decoding, with the missing exact-model admission only. */
class AstraStructured extends Structured
{
    protected function handleAutoMode(Request $request): Response
    {
        return $request->model() === 'gpt-6-astra' ? $this->handleStructuredMode($request) : parent::handleAutoMode($request);
    }

    protected function handleStructuredMode(Request $request): Response
    {
        if ($request->model() !== 'gpt-6-astra') {
            return parent::handleStructuredMode($request);
        }

        return $this->sendRequest($request, Arr::whereNotNull([
            'type' => 'json_schema', 'name' => preg_match('/\A[a-zA-Z0-9_-]{1,64}\z/', $request->schema()->name())
                ? $request->schema()->name() : 'schema_'.substr(hash('sha256', $request->schema()->name()), 0, 56), 'schema' => $request->schema()->toArray(),
            'strict' => $request->providerOptions('schema.strict'),
        ]));
    }
}
