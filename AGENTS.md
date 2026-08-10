> You are in **splicewire/laravel-beam-inference** — the public structured-generation substrate for the Splicewire estate.

A Prism-free `StructuredLlm` port plus its envelope value objects (`GenerationRequest`,
`GenerationResult`, `RawSchema`, `Usage`) and a plain vanilla `Prism::structured()` binding. A
satellite wires its own Prism/keys and reuses the shape; the Splicewire platform rebinds a metered
binding over the same port. The metering moat never enters this package.
