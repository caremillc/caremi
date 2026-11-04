
#!/usr/bin/env bash
set -Eeuo pipefail

# Careminate Day 24 clean-install milestone gate.
# Requires Git Bash (Windows) or Bash (Linux).
# Both source repositories must have committed revisions.

fail() {
    printf '[FAIL] %s\n' "$1" >&2
    exit 1
}

note() {
    printf '[DAY24] %s\n' "$1"
}

for tool in git php composer mktemp; do
    command -v "$tool" >/dev/null 2>&1 \
        || fail "Missing prerequisite: $tool"
done

APP_SOURCE="$(
    cd "$(dirname "${BASH_SOURCE[0]}")/.."
    pwd -P
)"

FRAMEWORK_SOURCE="${APP_SOURCE}/framework"

[[ -f "$APP_SOURCE/composer.json" ]] \
    || fail 'Application composer.json is missing.'

git -C "$APP_SOURCE" rev-parse \
    --is-inside-work-tree >/dev/null 2>&1 \
    || fail 'Application must be a Git checkout.'

git -C "$FRAMEWORK_SOURCE" rev-parse \
    --is-inside-work-tree >/dev/null 2>&1 \
    || fail 'Framework must be a Git checkout.'

APP_GIT_ROOT="$(
    git -C "$APP_SOURCE" rev-parse --show-toplevel
)"

FRAMEWORK_GIT_ROOT="$(
    git -C "$FRAMEWORK_SOURCE" rev-parse --show-toplevel
)"

[[ "$APP_GIT_ROOT" != "$FRAMEWORK_GIT_ROOT" ]] \
    || fail 'Application and framework must be separate Git repositories.'

# Use committed snapshots instead of mixing tracked and
# untracked development files.

[[ -z "$(git -C "$APP_SOURCE" status --porcelain)" ]] \
    || fail 'Application checkout has uncommitted changes.'

[[ -z "$(git -C "$FRAMEWORK_SOURCE" status --porcelain)" ]] \
    || fail 'Framework checkout has uncommitted changes.'

# Ensure required application files will survive a clean clone.

git -C "$APP_SOURCE" ls-files \
    --error-unmatch composer.lock >/dev/null 2>&1 \
    || fail 'Application composer.lock must be tracked by Git.'

git -C "$APP_SOURCE" ls-files \
    --error-unmatch scripts/http-smoke.php >/dev/null 2>&1 \
    || fail 'HTTP smoke runner must be tracked by Git.'

git -C "$APP_SOURCE" ls-files \
    --error-unmatch routes/web.php >/dev/null 2>&1 \
    || fail 'Application routes/web.php must be tracked by Git.'

git -C "$APP_SOURCE" ls-files \
    --error-unmatch bootstrap/http.php >/dev/null 2>&1 \
    || fail 'Application bootstrap/http.php must be tracked by Git.'

# Select a loopback port without using the application's
# normal development port.

PORT="${HTTP_SMOKE_PORT:-18080}"

[[ "$PORT" =~ ^[0-9]+$ ]] \
    || fail 'HTTP_SMOKE_PORT must be decimal digits.'

PORT="$((10#$PORT))"

(( PORT >= 1024 && PORT <= 65535 )) \
    || fail 'HTTP_SMOKE_PORT must be 1024-65535.'

php -r '
$listener = @stream_socket_server(
    "tcp://127.0.0.1:" . $argv[1]
);

if ($listener === false) {
    exit(1);
}

fclose($listener);
' "$PORT" || fail "Port $PORT is unavailable."

APP_REV="$(
    git -C "$APP_SOURCE" rev-parse HEAD
)"

FRAMEWORK_REV="$(
    git -C "$FRAMEWORK_SOURCE" rev-parse HEAD
)"

WORK="$(
    mktemp -d "${TMPDIR:-/tmp}/careminate-day24.XXXXXX"
)"

CLEAN_APP="$WORK/application"
SERVER_PID=''

cleanup() {
    local exit_code=$?

    trap - EXIT INT TERM

    if [[ -n "$SERVER_PID" ]]; then
        kill "$SERVER_PID" 2>/dev/null || true
        wait "$SERVER_PID" 2>/dev/null || true
    fi

    if [[ "${KEEP_REHEARSAL:-0}" == '1' ]]; then
        note "Retained rehearsal files: $WORK"
    else
        rm -rf -- "$WORK"
    fi

    exit "$exit_code"
}

trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

note "Application revision: $APP_REV"
note "Framework revision: $FRAMEWORK_REV"
note 'Creating independent clean clones.'

git clone \
    --quiet \
    --no-local \
    "$APP_SOURCE" \
    "$CLEAN_APP"

git clone \
    --quiet \
    --no-local \
    "$FRAMEWORK_SOURCE" \
    "$CLEAN_APP/framework"

# These files must exist in the committed application.
# They cannot be inherited from the developer's checkout.

for relative_path in \
    composer.lock \
    .env.example \
    careminate \
    bootstrap/app.php \
    bootstrap/routes.php \
    bootstrap/http.php \
    routes/web.php \
    public/index.php \
    scripts/http-smoke.php \
    docs/quick-start.md \
    docs/staging-plan.md
do
    [[ -f "$CLEAN_APP/$relative_path" ]] \
        || fail "Missing clean-install file: $relative_path"
done

# First verify the framework independently.

note 'Installing framework dependencies.'

(
    cd "$CLEAN_APP/framework"

    composer install \
        --no-interaction \
        --prefer-dist \
        --no-progress

    composer check
)

# Then install the actual application using its
# Composer path repository and committed lockfile.

note 'Installing application dependencies.'

(
    cd "$CLEAN_APP"

    cp .env.example .env

    composer validate --strict

    composer install \
        --no-interaction \
        --prefer-dist \
        --no-progress

    composer check

    php careminate --version

    php careminate route:list --format=json \
        > "$WORK/routes.json"
)

# Confirm the clean application registered the six
# reference routes and all three required endpoints.

php -r '
$routes = json_decode(
    file_get_contents($argv[1]),
    true,
    flags: JSON_THROW_ON_ERROR
);

if (!is_array($routes) || count($routes) !== 6) {
    exit(1);
}

$expected = [
    "GET /health",
    "GET /greet/{name}",
    "POST /api/echo",
];

$actual = [];

foreach ($routes as $route) {
    if (
        !is_array($route)
        || !is_string($route["method"] ?? null)
        || !is_string($route["path"] ?? null)
    ) {
        exit(1);
    }

    $actual[] = $route["method"] . " " . $route["path"];
}

foreach ($expected as $route) {
    if (!in_array($route, $actual, true)) {
        exit(1);
    }
}

echo "[PASS] Six registered application routes.\n";
' "$WORK/routes.json" \
    || fail 'Clean application route snapshot is incorrect.'

# Serve the cloned application, not the existing source.

note "Starting HTTP server on 127.0.0.1:$PORT"

cd "$CLEAN_APP"

php -S "127.0.0.1:$PORT" \
    -t public \
    public/index.php \
    > "$WORK/server.log" 2>&1 &

SERVER_PID=$!

ORIGIN="http://127.0.0.1:$PORT"
ready=0

for attempt in {1..30}; do
    if ! kill -0 "$SERVER_PID" 2>/dev/null; then
        fail 'Clean application HTTP server exited.'
    fi

    if php -r '
$context = stream_context_create([
    "http" => [
        "timeout" => 1,
        "follow_location" => 0,
    ],
]);

$body = @file_get_contents(
    $argv[1] . "/health",
    false,
    $context
);

exit($body === "healthy" ? 0 : 1);
' "$ORIGIN"; then
        ready=1
        break
    fi

    sleep 0.25
done

(( ready == 1 )) \
    || fail 'Clean application /health is unavailable.'

# Reuse the Day 23 live HTTP contract. No duplicated
# endpoint test definitions are needed.

note 'Running ten live HTTP smoke checks.'

if ! php scripts/http-smoke.php \
    "$ORIGIN" \
    --json \
    > "$WORK/http-smoke.json"
then
    php -r '
$report = json_decode(
    file_get_contents($argv[1]),
    true
);

foreach (($report["checks"] ?? []) as $check) {
    if (($check["passed"] ?? false) !== true) {
        fwrite(
            STDERR,
            "[FAIL] "
                . ($check["name"] ?? "HTTP check")
                . ": "
                . ($check["reason"] ?? "unknown failure")
                . "\n"
        );
    }
}
' "$WORK/http-smoke.json" || true

    fail 'Live HTTP smoke checks failed.'
fi

# Write evidence back to the original application.
# Report contains no request or response bodies.

OS_FAMILY="$(
    php -r 'echo strtolower(PHP_OS_FAMILY);'
)"

REPORT="$APP_SOURCE/docs/evidence/day24-clean-${OS_FAMILY}.json"

mkdir -p "$APP_SOURCE/docs/evidence"

php -r '
$report = json_decode(
    file_get_contents($argv[1]),
    true,
    flags: JSON_THROW_ON_ERROR
);

if (
    !is_array($report)
    || ($report["total"] ?? null) !== 10
    || ($report["passed"] ?? null) !== 10
    || ($report["failed"] ?? null) !== 0
) {
    exit(1);
}

$report["clean_install"] = [
    "application_commit" => $argv[2],
    "framework_commit" => $argv[3],
    "composer_lock_sha256" => hash_file(
        "sha256",
        $argv[4]
    ),
];

$encoded = json_encode(
    $report,
    JSON_PRETTY_PRINT
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR
) . "\n";

if (
    file_put_contents($argv[5], $encoded)
    !== strlen($encoded)
) {
    exit(1);
}

echo "[PASS] Clean install: 10/10 HTTP checks.\n";
' \
    "$WORK/http-smoke.json" \
    "$APP_REV" \
    "$FRAMEWORK_REV" \
    "$CLEAN_APP/composer.lock" \
    "$REPORT" \
    || fail 'Could not validate or save Day 24 evidence.'

note "Evidence saved to: $REPORT"
note 'Day 24 clean-skeleton rehearsal passed.'
