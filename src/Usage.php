<?php

namespace Rushing\Inference;

/**
 * Token usage of one structured-generation call. A Prism-free value object so {@see GenerationResult}
 * carries usage without depending on `prism-php/prism`; a host maps Prism's own Usage into this at the
 * LLM seam.
 */
class Usage
{
    public function __construct(
        public int $promptTokens = 0,
        public int $completionTokens = 0,
    ) {}
}
