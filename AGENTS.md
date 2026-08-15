> You are in **rushing/laravel-inference** — a vendor-neutral structured-generation substrate.

A Prism-free `StructuredLlm` port plus its envelope value objects (`GenerationRequest`,
`GenerationResult`, `RawSchema`, `Usage`) and a plain vanilla `Prism::structured()` binding. A
consumer wires its own Prism/keys and reuses the shape; a platform may rebind a metered binding over
the same port. The metering moat never enters this package.

Namespaced `Rushing\Inference\`, with the agent seam nested at `Rushing\Inference\Agent\` — one PSR-4
root over `src/`. Requires nothing but `php`, `illuminate/contracts`, `illuminate/support` and
`prism-php/prism`; it depends on no `splicewire/*` package and must not grow one. That neutrality is
the reason it lives in the `rushing` vendor rather than `splicewire` (relocated by splicewire-app
ADR-0205; the metering moat rule is ADR-0125).
