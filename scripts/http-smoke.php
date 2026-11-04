<?php

declare(strict_types=1);

/**
 * Careminate live HTTP smoke runner (PHP 8.4+).
 *
 * Usage:
 *   php scripts/http-smoke.php [base-url] [--json]
 *
 * Exit codes:
 *   0  All smoke checks passed.
 *   1  One or more smoke checks failed.
 *   64 Invalid command arguments.
 *
 * Requires allow_url_fopen and the HTTP stream wrapper.
 */

const HTTP_SMOKE_MAX_RESPONSE_BYTES = 65536;
const HTTP_SMOKE_TIMEOUT_SECONDS = 5.0;

/**
 * Execute an HTTP request.
 *
 * @return array{
 *     status: int,
 *     headers: array<string, string>,
 *     body: string
 * }|null
 */
function smokeRequest(
    string $baseUrl,
    string $method,
    string $path,
    string $body = '',
    ?string $contentType = null,
): ?array {
    $headers = [
        'Accept: application/json, text/plain',
        'Accept-Encoding: identity',
        'Connection: close',
    ];

    if ($contentType !== null) {
        $headers[] = 'Content-Type: ' . $contentType;
    }

    if ($method === 'POST') {
        $headers[] = 'Content-Length: ' . strlen($body);
    }

    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers) . "\r\n",
            'content' => $body,
            'ignore_errors' => true,
            'follow_location' => 0,
            'max_redirects' => 0,
            'protocol_version' => 1.1,
            'timeout' => HTTP_SMOKE_TIMEOUT_SECONDS,
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    // Suppress transport warnings that may disclose local paths.
    $stream = @fopen(
        $baseUrl . $path,
        'rb',
        false,
        $context,
    );

    if ($stream === false) {
        return null;
    }

    try {
        // Read an extra byte to detect oversized responses.
        $responseBody = stream_get_contents(
            $stream,
            HTTP_SMOKE_MAX_RESPONSE_BYTES + 1,
        );

        $metadata = stream_get_meta_data($stream);
    } finally {
        fclose($stream);
    }

    if (
        ! is_string($responseBody)
        || strlen($responseBody) > HTTP_SMOKE_MAX_RESPONSE_BYTES
        || $metadata['timed_out'] === true
    ) {
        return null;
    }

    $headerLines = $metadata['wrapper_data'] ?? null;

    if (! is_array($headerLines)) {
        return null;
    }

    $status = null;
    $responseHeaders = [];

    foreach ($headerLines as $line) {
        if (! is_string($line)) {
            continue;
        }

        if (
            preg_match(
                '~\AHTTP/\d(?:\.\d)?\s+([1-5]\d{2})(?:\s|$)~i',
                $line,
                $matches,
            ) === 1
        ) {
            // Each status line begins a new response header block.
            $status = (int) $matches[1];
            $responseHeaders = [];

            continue;
        }

        $separator = strpos($line, ':');

        if ($separator === false || $status === null) {
            continue;
        }

        $key = strtolower(
            trim(substr($line, 0, $separator)),
        );

        $responseHeaders[$key] = trim(
            substr($line, $separator + 1),
        );
    }

    if ($status === null) {
        return null;
    }

    return [
        'status' => $status,
        'headers' => $responseHeaders,
        'body' => $responseBody,
    ];
}

/**
 * Create a typed smoke-test definition.
 *
 * @param array<string, string>|null $expectedJson
 *
 * @return array{
 *     name: string,
 *     method: string,
 *     path: string,
 *     status: int,
 *     body: string,
 *     content_type: ?string,
 *     expected_body: ?string,
 *     expected_json: ?array<string, string>,
 *     error_code: ?string,
 *     allow: ?string
 * }
 */
function smokeCase(
    string $name,
    string $method,
    string $path,
    int $status,
    string $body = '',
    ?string $contentType = null,
    ?string $expectedBody = null,
    ?array $expectedJson = null,
    ?string $errorCode = null,
    ?string $allow = null,
): array {
    return [
        'name' => $name,
        'method' => $method,
        'path' => $path,
        'status' => $status,
        'body' => $body,
        'content_type' => $contentType,
        'expected_body' => $expectedBody,
        'expected_json' => $expectedJson,
        'error_code' => $errorCode,
        'allow' => $allow,
    ];
}

/**
 * Compare the HTTP response with the expected contract.
 *
 * @param array{
 *     name: string,
 *     method: string,
 *     path: string,
 *     status: int,
 *     body: string,
 *     content_type: ?string,
 *     expected_body: ?string,
 *     expected_json: ?array<string, string>,
 *     error_code: ?string,
 *     allow: ?string
 * } $case
 *
 * @param array{
 *     status: int,
 *     headers: array<string, string>,
 *     body: string
 * }|null $response
 *
 * @return array{
 *     name: string,
 *     passed: bool,
 *     expected_status: int,
 *     actual_status: ?int,
 *     reason: string
 * }
 */
function smokeCheck(
    array $case,
    ?array $response,
): array {
    $reason = '';

    if ($response === null) {
        $reason = 'Connection failed, timed out, or invalid/oversized response.';
    } elseif ($response['status'] !== $case['status']) {
        $reason = 'Unexpected HTTP status.';
    } elseif (
        $case['expected_body'] !== null
        && $response['body'] !== $case['expected_body']
    ) {
        $reason = 'Unexpected response body.';
    } elseif ($case['allow'] !== null) {
        $allowedMethods = array_map(
            'trim',
            explode(
                ',',
                $response['headers']['allow'] ?? '',
            ),
        );

        if (
            ! in_array(
                $case['allow'],
                $allowedMethods,
                true,
            )
        ) {
            $reason = 'Missing expected Allow header method.';
        }
    }

    if (
        $reason === ''
        && (
            $case['expected_json'] !== null
            || $case['error_code'] !== null
        )
    ) {
        $contentType = strtolower(
            trim(
                $response['headers']['content-type'] ?? '',
            ),
        );

        if (
            preg_match(
                '~\Aapplication/json(?:\s*;|$)~',
                $contentType,
            ) !== 1
        ) {
            $reason = 'Expected application/json Content-Type.';
        } else {
            try {
                $decoded = json_decode(
                    $response['body'],
                    true,
                    flags: JSON_THROW_ON_ERROR,
                );
            } catch (JsonException) {
                $decoded = null;
            }

            if (! is_array($decoded)) {
                $reason = 'Expected a JSON object.';
            } elseif (
                $case['expected_json'] !== null
                && $decoded !== $case['expected_json']
            ) {
                $reason = 'Unexpected JSON payload.';
            } elseif ($case['error_code'] !== null) {
                $error = $decoded['error'] ?? null;

                if (
                    ! is_array($error)
                    || ($error['code'] ?? null)
                        !== $case['error_code']
                ) {
                    $reason = 'Unexpected JSON error code.';
                }
            }
        }
    }

    return [
        'name' => $case['name'],
        'passed' => $reason === '',
        'expected_status' => $case['status'],
        'actual_status' => $response['status'] ?? null,
        'reason' => $reason,
    ];
}

/*
|--------------------------------------------------------------------------
| Command-line argument parsing
|--------------------------------------------------------------------------
*/

$baseUrl = 'http://127.0.0.1:8080';
$jsonOutput = false;
$urlProvided = false;

// PHPStan cannot guarantee that the global $argv exists.
$rawArguments = $_SERVER['argv'] ?? [];

if (! is_array($rawArguments)) {
    fwrite(
        STDERR,
        "Invalid CLI argument environment.\n",
    );

    exit(64);
}

foreach (array_slice($rawArguments, 1) as $argument) {
    if (! is_string($argument)) {
        fwrite(
            STDERR,
            "Invalid CLI argument.\n",
        );

        exit(64);
    }

    if ($argument === '--help') {
        echo "Usage: php scripts/http-smoke.php [base-url] [--json]\n";

        exit(0);
    }

    if ($argument === '--json' && ! $jsonOutput) {
        $jsonOutput = true;

        continue;
    }

    if (
        ! str_starts_with($argument, '-')
        && ! $urlProvided
    ) {
        $baseUrl = $argument;
        $urlProvided = true;

        continue;
    }

    fwrite(
        STDERR,
        "Invalid smoke-test arguments. Use --help.\n",
    );

    exit(64);
}

/*
|--------------------------------------------------------------------------
| Base URL validation
|--------------------------------------------------------------------------
*/

$parts = parse_url($baseUrl);

if (
    ! is_array($parts)
    || ! in_array(
        $parts['scheme'] ?? null,
        ['http', 'https'],
        true,
    )
    || ! isset($parts['host'])
    || $parts['host'] === ''
    || isset($parts['user'])
    || isset($parts['pass'])
    || isset($parts['query'])
    || isset($parts['fragment'])
    || ! in_array(
        $parts['path'] ?? '',
        ['', '/'],
        true,
    )
    || (
        isset($parts['port'])
        && $parts['port'] < 1
    )
    || preg_match('/[\x00-\x20\x7F]/', $baseUrl) === 1
    || preg_match('//u', $baseUrl) !== 1
) {
    fwrite(
        STDERR,
        "Invalid base URL. Use an HTTP(S) origin without a path, query, or credentials.\n",
    );

    exit(64);
}

$baseUrl = rtrim($baseUrl, '/');

/*
|--------------------------------------------------------------------------
| HTTP smoke-test definitions
|--------------------------------------------------------------------------
*/

/**
 * @var list<array{
 *     name: string,
 *     method: string,
 *     path: string,
 *     status: int,
 *     body: string,
 *     content_type: ?string,
 *     expected_body: ?string,
 *     expected_json: ?array<string, string>,
 *     error_code: ?string,
 *     allow: ?string
 * }> $checks
 */
$checks = [
    smokeCase(
        'health',
        'GET',
        '/health',
        200,
        expectedBody: 'healthy',
    ),

    smokeCase(
        'greeting',
        'GET',
        '/greet/Ada',
        200,
        expectedJson: [
            'greeting' => 'Hello, Ada!',
        ],
    ),

    smokeCase(
        'echo valid JSON',
        'POST',
        '/api/echo',
        200,
        body: '{"message":"Hello Careminate"}',
        contentType: 'application/json',
        expectedJson: [
            'message' => 'Hello Careminate',
        ],
    ),

    smokeCase(
        'echo malformed JSON',
        'POST',
        '/api/echo',
        400,
        body: '{"message":',
        contentType: 'application/json',
        errorCode: 'invalid_json',
    ),

    smokeCase(
        'echo unsupported type',
        'POST',
        '/api/echo',
        415,
        body: '{"message":"Hello"}',
        contentType: 'text/plain',
        errorCode: 'unsupported_media_type',
    ),

    smokeCase(
        'echo invalid fields',
        'POST',
        '/api/echo',
        422,
        body: '{"message":"Hello","extra":true}',
        contentType: 'application/json',
        errorCode: 'invalid_message',
    ),

    smokeCase(
        'echo oversized',
        'POST',
        '/api/echo',
        413,
        body: '{"message":"'
            . str_repeat('x', 1025)
            . '"}',
        contentType: 'application/json',
        errorCode: 'body_too_large',
    ),

    smokeCase(
        'greeting invalid parameter',
        'GET',
        '/greet/123',
        404,
    ),

    smokeCase(
        'echo rejects GET',
        'GET',
        '/api/echo',
        405,
        allow: 'POST',
    ),

    smokeCase(
        'unknown route',
        'GET',
        '/this-route-does-not-exist',
        404,
    ),
];

/*
|--------------------------------------------------------------------------
| Execute the HTTP checks
|--------------------------------------------------------------------------
*/

$results = [];
$passed = 0;

foreach ($checks as $case) {
    $response = smokeRequest(
        $baseUrl,
        $case['method'],
        $case['path'],
        $case['body'],
        $case['content_type'],
    );

    $result = smokeCheck(
        $case,
        $response,
    );

    $results[] = $result;

    if ($result['passed']) {
        ++$passed;
    }

    if (! $jsonOutput) {
        printf(
            "[%s] %s (expected %d, received %s)%s\n",
            $result['passed'] ? 'PASS' : 'FAIL',
            $result['name'],
            $result['expected_status'],
            $result['actual_status'] === null
                ? 'none'
                : (string) $result['actual_status'],
            $result['reason'] === ''
                ? ''
                : ' - ' . $result['reason'],
        );
    }
}

/*
|--------------------------------------------------------------------------
| Build the smoke report
|--------------------------------------------------------------------------
*/

$report = [
    'schema_version' => 1,
    'timestamp_utc' => gmdate('c'),
    'os_family' => PHP_OS_FAMILY,
    'php_version' => PHP_VERSION,
    'base_url' => $baseUrl,
    'total' => count($checks),
    'passed' => $passed,
    'failed' => count($checks) - $passed,
    'checks' => $results,
];

/*
|--------------------------------------------------------------------------
| Emit report and exit
|--------------------------------------------------------------------------
*/

if ($jsonOutput) {
    echo json_encode(
        $report,
        JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR,
    ) . "\n";
} else {
    printf(
        "HTTP smoke results: %d/%d passed.\n",
        $passed,
        count($checks),
    );
}

exit(
    $passed === count($checks)
        ? 0
        : 1
);
