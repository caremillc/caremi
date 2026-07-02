# Careminate Environment Loader

The Environment Loader loads environment variables from `.env` files before the framework configuration and services are booted.

## Responsibilities

The Environment Loader is responsible for:

- Loading `.env` files
- Loading environment-specific files
- Providing the `env()` helper
- Casting common value types
- Preventing accidental overrides
- Validating required environment keys

## Supported Files

Careminate supports:

```txt
.env
.env.local
.env.testing
.env.production

The base .env file is loaded first.

Environment-specific files are loaded based on APP_ENV.

Example:

APP_ENV=production

Careminate will try to load:

.env
.env.production
.env.local
Loading Environment Values

Usually this happens in bootstrap/app.php.

use Careminate\Support\Env;

Env::load(dirname(__DIR__));
Reading Values

Use the env() helper:

$name = env('APP_NAME', 'Careminate');

Or use the class directly:

$name = Env::get('APP_NAME', 'Careminate');
Setting Values
Env::set('APP_DEBUG', false);
Checking Values
if (Env::has('APP_KEY')) {
    //
}
Required Values

You may require specific keys during bootstrap.

Env::require([
    'APP_ENV',
    'APP_KEY',
    'DB_DATABASE',
]);

If a required key is missing, Careminate throws a runtime exception.

Value Casting

The Environment Loader casts common values automatically.

APP_DEBUG=true
CACHE_ENABLED=false
APP_PORT=8000
PRICE_RATE=10.5
EMPTY_VALUE=empty
NULL_VALUE=null

Results:

true       => boolean true
false      => boolean false
8000       => integer
10.5       => float
empty      => empty string
null       => null

Quoted values remain strings:

APP_NAME="Careminate Framework"
Example .env
APP_NAME=Careminate
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/caremi/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caremi
DB_USERNAME=root
DB_PASSWORD=
Bootstrap Example
use Careminate\Foundation\Application;
use Careminate\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__));

$app = new Application(dirname(__DIR__));

$app->setEnvironment((string) env('APP_ENV', 'production'));

return $app;
Config Usage

Environment values should usually be read inside config files.

return [
    'name' => env('APP_NAME', 'Careminate'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
];

After configuration caching is introduced, application code should prefer:

config('app.name')

instead of calling:

env('APP_NAME')

directly outside config files.

Developer Use Cases
Configure database
'host' => env('DB_HOST', '127.0.0.1')
Configure mail
'mailer' => env('MAIL_MAILER', 'smtp')
Configure cache
'default' => env('CACHE_DRIVER', 'file')
Configure queue
'default' => env('QUEUE_CONNECTION', 'sync')
End-User Impact

End users benefit from environment-based configuration because the app can safely adapt between local, testing, staging, and production deployments.

This improves:

Security
Stability
Deployment consistency
Debug behavior
Database safety
Best Practices
Never commit real production secrets.
Keep .env.example in version control.
Keep .env out of version control.
Use env() mainly inside config files.
Use config() in application code after the config layer exists.
Always define APP_ENV.
Always define APP_KEY before encryption is implemented.
Do not store complex arrays in .env.
Troubleshooting
Value is not loading

Check that the .env file exists in the project root.

Wrong environment file is loaded

Check:

APP_ENV=local
Boolean value behaves like a string

Use unquoted values:

APP_DEBUG=true

not:

APP_DEBUG="true"
Missing required values

Add the missing key to .env.

APP_KEY=base64:example
Next Feature
Phase 1 — Feature 5: Configuration Repository

---

# Feature 4 Complete

Next:

```txt
Phase 1 — Feature 5: Configuration Repository
