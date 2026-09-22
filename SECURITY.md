# Security Policy

WooCompat Auditor runs inside WordPress and can expose diagnostic context to authorized administrators. Security and output hygiene are therefore part of the project design.

## Supported versions

Security fixes are provided for the latest release line while the project is in early development.

## Reporting a vulnerability

Please do not open a public issue for a vulnerability that could expose credentials, customer information, privileged actions, or sensitive server details.

Use GitHub's private vulnerability reporting feature for this repository when available. Include reproduction steps, affected versions, impact, and a minimal proof of concept.

## Scope expectations

The project intentionally avoids remote telemetry in the current release. Exported reports should not contain passwords, API keys, nonces, authentication cookies, customer records, or absolute filesystem paths.
