<?php

namespace Rushing\Inference\Prism;

use Prism\Prism\Structured\Request;
use Prism\Prism\Structured\Response;

/** Compatibility for the exact documented Astra alias until Prism's model list includes it. */
class OpenAI extends \Prism\Prism\Providers\OpenAI\OpenAI
{
    public function structured(Request $request): Response
    {
        if ($request->model() !== 'gpt-6-astra') {
            return parent::structured($request);
        }

        return (new AstraStructured($this->client($request->clientOptions(), $request->clientRetry())))->handle($request);
    }
}
