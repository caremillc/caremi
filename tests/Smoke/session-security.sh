
#!/usr/bin/env bash

set -euo pipefail

BASE="http://127.0.0.1:8099"
TMP="$(mktemp -d)"

trap 'rm -rf "$TMP"' EXIT

ACTIVE="$TMP/active.cookies"
OLD="$TMP/old.cookies"
HEADERS="$TMP/logout.headers"

check_json() {
    local key="$1"
    local expected="$2"

    php -r '
        $data = json_decode(
            stream_get_contents(STDIN),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $key = $argv[1];
        $expected = $argv[2] === "true";

        exit(
            is_array($data)
            && ($data[$key] ?? null) === $expected
                ? 0
                : 1
        );
    ' "$key" "$expected"
}

echo "=== Careminate Session Security ==="

# 1. Diagnostic login
response="$(curl -fsS \
    -X POST \
    -c "$ACTIVE" \
    "$BASE/__session_probe_login")"

printf '%s' "$response" |
    check_json authenticated true

echo "[PASS] Diagnostic login"

# Ensure a session cookie was actually saved.
if ! awk '
    /^#HttpOnly_/ || /^[^#]/ {
        if (NF >= 7) found = 1
    }
    END { exit !found }
' "$ACTIVE"; then
    echo "[FAIL] No session cookie saved"
    exit 1
fi

cp "$ACTIVE" "$OLD"

echo "[PASS] Session cookie saved"

# 2. Authentication persistence
response="$(curl -fsS \
    -b "$ACTIVE" \
    "$BASE/__session_probe_identity")"

printf '%s' "$response" |
    check_json authenticated true

echo "[PASS] Authentication persisted"

# 3. Logout
response="$(curl -fsS \
    -X POST \
    -b "$ACTIVE" \
    -c "$ACTIVE" \
    -D "$HEADERS" \
    "$BASE/__session_probe_logout")"

printf '%s' "$response" |
    check_json logged_out true

echo "[PASS] Logout completed"

# 4. Verify cookie expiration instruction
if ! grep -Eqi \
    '^Set-Cookie:.*(Expires=|Max-Age=0)' \
    "$HEADERS"; then
    echo "[FAIL] Cookie expiration missing"
    exit 1
fi

echo "[PASS] Cookie expiration header present"

# 5. Replay original cookie
response="$(curl -fsS \
    -b "$OLD" \
    "$BASE/__session_probe_identity")"

printf '%s' "$response" |
    check_json authenticated false

echo "[PASS] Old session cannot restore identity"

# 6. Verify current browser state
response="$(curl -fsS \
    -b "$ACTIVE" \
    "$BASE/__session_probe_identity")"

printf '%s' "$response" |
    check_json authenticated false

echo "[PASS] Current browser is logged out"

echo
echo "[PASS] Session security smoke test completed"
