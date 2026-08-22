# Careminate

Careminate is an enterprise-oriented PHP framework under incremental development. This repository contains both the application project and the framework package used through a Composer path repository.

The current release includes the Engineering Foundation, Contracts and Dependency Container, and Application Kernel milestones. It provides package boundaries, quality gates, CI, exception roots, cross-platform path handling, semantic version management, public container contracts, PSR-11 resolution, explicit service lifetimes, autowiring, contextual injection, lazy services, compiled container metadata, explicit application state, ordered bootstrap, request and console runtime boundaries, graceful termination, and production container-cache modes. Service providers, modules, and configuration are intentionally scheduled for later phases.

## Requirements

- PHP 8.4 or newer
- Composer 2
- PHP extensions required by PHPUnit: DOM, JSON, LibXML, Mbstring, XML, and XMLWriter

## Install

```bash
composer install
```

From Windows PowerShell with XAMPP PHP:

```powershell
Set-Alias php C:\xampp\php\php.exe
php C:\ProgramData\ComposerSetup\bin\composer.phar install
```

If `composer` is already on `PATH`, use `composer install` directly.

## Verify

```bash
composer quality
composer rector-check
composer audit --locked
```

See [docs/FOUNDATION.md](docs/FOUNDATION.md), [docs/CONTAINER.md](docs/CONTAINER.md), [docs/APPLICATION_KERNEL.md](docs/APPLICATION_KERNEL.md), and [docs/adr](docs/adr) for complete guides and architecture decisions.

## Package layout

```text
caremi/
├── app/                 Application code (future phases)
├── docs/                Feature guides and ADRs
├── framework/           Distributable careminate/framework package
│   ├── src/             Framework runtime source
│   └── tests/           Framework tests
└── tests/               Application/repository integration tests
```

## License

Careminate is released under the MIT License.