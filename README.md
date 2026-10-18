# rushing/laravel-inference

A **vendor-neutral structured-generation substrate**: a Prism-free `StructuredLlm` port plus its
envelope value objects, and a plain vanilla `Prism::structured()` binding. A consumer wires its own
Prism/keys and reuses the shape; a platform may rebind a metered binding over the same port — the
metering moat never enters this package (ADR-0125; relocated to the `rushing` vendor by
splicewire-app ADR-0205, and it must not grow a `splicewire/*` dependency).

```bash
composer require rushing/laravel-inference
```

The seam, under `src/` (`Rushing\Inference\`):

- `StructuredLlm` — the port: structured generation behind one interface;
- `GenerationRequest`, `GenerationResult`, `RawSchema`, `Usage` — the envelope value objects;
- `Prism/` — the vanilla `Prism::structured()` adapter (bring your own keys);
- `ClosureStructuredLlm` — the test/fake adapter, a closure behind the same port;
- `Agent/` — the agent seam, nested under the same PSR-4 root;
- `InferenceServiceProvider` — default binding (`bindIf`, so a host supersedes cleanly).

Start with [`AGENTS.md`](AGENTS.md). Tests: `composer test`.
