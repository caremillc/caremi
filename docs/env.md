# Environment Configuration System

## Overview

This system provides secure environment management including:
- `.env` file generation
- Environment validation
- Encryption services
- Key generation

## Key Components

1. `.env` File Management
2. APP_KEY Generation & Encryption
3. Environment Validation
4. Console Commands

## Installation

1. Ensure `vlucas/phpdotenv` is installed via Composer
2. Place the provided files in your project structure

## Commands

### Generate .env File

```bash
php caremi env:generate
```

Creates a .env file from .env.example with:

    All original variables

    A secure 64-byte base64-encoded APP_KEY

# Validate Environment
```bash 
php caremi env:validate
```
Checks for:

    Required variables (APP_NAME, APP_ENV, APP_DEBUG, APP_KEY)

    Valid APP_KEY format (base64 or 64+ chars)

# Generate New APP_KEY

```bash 
php caremi key:generate
```

Options:

    --force: Skip confirmation and clean storage

Generates a new secure key and:

    Updates .env file

    Optionally clears storage directories

# Encrypt Data

```bash 
php caremi encrypt "your data"
```

Uses APP_KEY to encrypt strings
File Structure

caremi/
├── .env.example          # Template with required vars
├── caremi               # CLI entry point
├── config/
│   └── console.php      # Command registry
├── framework/
│   └── Careminate/
│       ├── Console/     # Command classes
│       ├── Security/    # Encryption
│       └── Support/     # Helpers
└── bootstrap/
    └── app.php          # Application bootstrap

# Security Implementation
## Encryption Details

    Uses AES-256-CBC

    Requires 64-byte key (base64 encoded in .env)

    Automatic IV generation

Example APP_KEY format:
```bash 
APP_KEY=base64:FEaiGupY4rKwXx42mriSNGd+Ave5iOwfoCDhvazeReGOrA5vEFtcYiFjtHpJIauqSV+2N8QFWfMnB4n6pXbkqQ==
```

# Usage Examples
## In Application Code
```php

// Get env vars
$debug = env('APP_DEBUG', false);

// Encrypt data
$encrypted = encrypt('sensitive data');

// Decrypt data
$decrypted = decrypt($encrypted);
```

# Session Encryption
```php

$session = new Session();
$session->put('secret', $data); // Auto-encrypts
$value = $session->get('secret'); // Auto-decrypts
```
# Best Practices

    Never commit .env - Add to .gitignore

    Rotate keys when compromised:
```bash

php caremi key:generate --force
```

# Validate after deployment:
```bash

    php caremi env:validate

    Use base64 keys for better entropy
```

# Troubleshooting
## Common Issues

❌ Missing .env.example

    Ensure file exists in project root

❌ Invalid APP_KEY

    Regenerate with key:generate

    Must be either:

        64+ character string, or

        base64 encoded 64-byte value

⚠️ Encryption errors after key change

    Run with --force to clear storage

    All users will need to re-authenticate



# This documentation covers:
1. Command usage
2. Security implementation
3. Integration examples
4. Maintenance procedures


