
#!/usr/bin/env bash

set -euo pipefail

BASE="http://127.0.0.1:8099"
TMP="$(mktemp -d)"

trap 'rm -rf "$TMP"' EXIT

COOKIE="$TMP/session.cookies"
BODY="$TMP/response.body"

echo "=== Careminate Session Exception Test ==="

# Establish a session before testing an exception.
response="$(curl -fsS \
    -c "$COOKIE" \
    "$BASE/__session_probe")"

if ! printf '%s' "$response" | php -r '
    $data = json_decode(
        stream_get_contents(STDIN),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    exit(
        is_array($data)
        && ($data["visits"] ?? null) === 1
            ? 0
            : 1
    );
'; then
    echo "[FAIL] Could not establish diagnostic session"
    exit 1
fi

echo "[PASS] Initial session established"

# Trigger the intentional controller exception.
status="$(curl -sS \
    -b "$COOKIE" \
    -c "$COOKIE" \
    -o "$BODY" \
    -w '%{http_code}' \
    "$BASE/__session_probe_failure")"

if [[ "$status" != "500" ]]; then
    echo "[FAIL] Expected HTTP 500, received $status"
    exit 1
fi

echo "[PASS] Controller exception returned HTTP 500"

# A subsequent request must recover data saved
# immediately before the exception.
response="$(curl -fsS \
    -b "$COOKIE" \
    -c "$COOKIE" \
    "$BASE/__session_probe_recovery")"

if ! printf '%s' "$response" | php -r '
    $data = json_decode(
        stream_get_contents(STDIN),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    exit(
        is_array($data)
        && ($data["recovered"] ?? null) === true
            ? 0
            : 1
    );
'; then
    echo "[FAIL] Session recovery failed"
    exit 1
fi

echo "[PASS] Session data survived handler exception"
echo "[PASS] HTTP exception cleanup verified"
