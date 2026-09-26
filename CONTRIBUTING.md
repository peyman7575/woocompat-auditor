# Contributing

Thanks for helping improve WooCompat Auditor.

## Principles

Contributions should keep the auditor:

- read-only by default;
- useful to real WooCommerce troubleshooting and release workflows;
- conservative about declaring a site "broken";
- careful not to expose credentials, tokens, customer data, or absolute paths;
- compatible with supported WordPress/WooCommerce environments.

## Local development

Requirements:

- PHP 7.4+
- Composer 2

Install development dependencies and run the full quality gate:

```bash
composer install
composer check
```

Individual checks:

```bash
composer lint
composer test
composer verify-version
```

Build the same installable ZIP shape used by release automation:

```bash
bash scripts/build-zip.sh
```

## Workflow

1. Open or reference an issue for non-trivial changes.
2. Create a focused branch.
3. Keep unrelated refactors out of the same pull request.
4. Add or update tests when behavior changes.
5. Run `composer check` before opening the pull request.
6. Explain how the change was validated and what false positives are possible.

## Audit checks

A new check should have:

- a stable machine-readable ID;
- a short human-readable label;
- one of the supported statuses: `pass`, `warning`, `fail`, or `info`;
- a plain-language message;
- only the minimum technical context required to investigate the result.

Warnings should be actionable signals, not assumptions. A check must not expose secrets, customer/order data, authentication material, or absolute filesystem paths.

## Pull requests

A pull request should be narrow enough to review and should include:

- the problem being solved;
- the behavior before and after the change;
- test coverage or a reason tests are not applicable;
- compatibility considerations;
- privacy/security considerations when diagnostic output changes.

CI must be green before merge.
