# Security Policy

## Supported versions

Careminate is currently pre-release software. Security fixes are applied to the latest development line until the first stable release establishes a longer support policy.

## Reporting a vulnerability

Do not open a public issue for a suspected vulnerability. Report it privately to the repository owner with:

- The affected component and version
- Reproduction steps or a proof of concept
- Expected and observed behavior
- Potential impact
- Any suggested mitigation

Do not include production credentials, personal data, tokens, private keys, or unredacted secrets. Maintainers should acknowledge a report, assess severity, coordinate a fix and advisory, and credit the reporter when requested and appropriate.

## Supply-chain controls

- Composer dependencies are locked at the application level.
- CI validates both Composer manifests and runs `composer audit --locked`.
- Dependabot monitors Composer packages and GitHub Actions.
- CI actions use declared major release lines; production repositories should adopt immutable commit pinning under their organizational policy.

