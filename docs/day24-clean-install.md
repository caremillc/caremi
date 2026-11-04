# Day 24 — Clean Skeleton Installation Rehearsal

**Milestone:** First usable Careminate HTTP application  
**Status:** Pending execution evidence

## Goal

Verify that committed application and framework revisions can be installed from scratch, without relying on untracked source files, a pre-existing vendor directory or undocumented configuration.

## Required source layout

The original development checkout is expected to contain:

```text
caremi/
├── composer.json
├── composer.lock
├── scripts/
│   ├── http-smoke.php
│   └── rehearse-clean-skeleton.sh
├── framework/
│   └── composer.json
└── public/
    └── index.php
```

The `framework/` directory is a separate Git repository. It is intentionally ignored by the application Git repository.

The application `composer.lock` must be committed.

## Prerequisites

Git, Composer, PHP 8.4 or newer, an available HTTP stream wrapper and Bash are required.

Windows execution uses Git Bash. Linux execution uses Bash.

Composer dependencies may require network access unless they are already available in a trusted local cache.

Both Git repositories must contain committed versions of the intended implementation, and both working trees must be clean.

## Execute

From the application root:

```bash
bash -n scripts/rehearse-clean-skeleton.sh
bash scripts/rehearse-clean-skeleton.sh
```

The script creates temporary independent clones, installs both projects' dependencies, runs their Composer quality gates, validates the six application routes and starts a local HTTP server.

It then runs the ten Day 23 smoke checks against that clean installation.

No existing XAMPP Apache service or manually started PHP development server is required.

## Expected result

```text
[PASS] Six registered application routes.
[PASS] Clean install: 10/10 HTTP checks.
[DAY24] Day 24 clean-skeleton rehearsal passed.
```

The framework and application PHPUnit totals are reported by their respective `composer check` commands.

The HTTP smoke suite is an additional integration check, not part of the PHPUnit test count.

## Evidence

Successful runs write an evidence report to:

- Windows: `docs/evidence/day24-clean-windows.json`
- Linux: `docs/evidence/day24-clean-linux.json`

Reports contain HTTP check results, operating system, PHP version, timestamp, application commit, framework commit and the application Composer lockfile's SHA-256 hash.

Reports do not include request or response bodies.

Review reports before sharing or committing them.

## Failure investigation

The script stops immediately if a source checkout is dirty, the application lockfile is untracked, a required skeleton file is missing, dependency installation fails, either quality gate fails, route registration differs from the expected contract, or an HTTP smoke check fails.

For debugging, retain the temporary clone and HTTP server log:

```bash
KEEP_REHEARSAL=1 bash scripts/rehearse-clean-skeleton.sh
```

The retained directory is displayed in the command output. Review its log locally and remove the temporary directory when no longer needed.

If port 18080 is unavailable, select a different unused local port:

```bash
HTTP_SMOKE_PORT=18081 bash scripts/rehearse-clean-skeleton.sh
```

A successful Windows run does not establish Linux compatibility. The gate must be executed independently on the intended Linux environment and supported PHP versions.

## Acceptance decision

Day 24 passes for an environment only when:

1. A clean clone of each repository is used.
2. Composer dependencies install successfully.
3. Framework and application quality gates pass.
4. All six reference routes are registered.
5. All ten live HTTP checks pass.
6. The evidence report identifies the exact tested revisions.

The overall web milestone remains pending until the required supported-platform evidence is available.