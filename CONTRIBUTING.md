# Contributing

Thanks for helping improve WooCompat Auditor.

## Principles

Contributions should keep the auditor:

- read-only by default;
- useful to real WooCommerce troubleshooting and release workflows;
- conservative about declaring a site "broken";
- careful not to expose credentials, tokens, customer data, or absolute paths;
- compatible with supported WordPress/WooCommerce environments.

## Workflow

1. Open or reference an issue for non-trivial changes.
2. Create a focused branch.
3. Keep unrelated refactors out of the same pull request.
4. Run PHP syntax checks and coding standards locally.
5. Explain how the check was validated and what false positives are possible.

## Audit checks

A new check should have a stable machine-readable ID, a short human label, a status, a plain-language message, and only the minimum technical context required to investigate the result.

Warnings should be actionable signals, not assumptions.
