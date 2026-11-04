<?php

declare(strict_types=1);

namespace App\Http;

use Careminate\Http\Request;
use Careminate\Http\Response;
use JsonException;
use stdClass;

/**
 * Reference HTTP handlers.
 *
 * No database access, external requests or persistent mutations.
 */
final class ReferenceEndpoints
{
    private const int MAX_JSON_BODY_BYTES = 1024;
    private const int MAX_MESSAGE_BYTES = 120;

    public static function greeting(Request $request): Response
    {
        $name = $request->routeParameter('name');

        if ($name === null) {
            return self::error(
                400,
                'invalid_name',
                'A name is required.',
            );
        }

        return self::json(200, [
            'greeting' => 'Hello, ' . $name . '!',
        ]);
    }

    public static function echoJson(Request $request): Response
    {
        $contentType = $request->header('content-type');

        if (
            $contentType === null
            || preg_match(
                '~\Aapplication/json(?:\s*;\s*charset=utf-8)?\z~iD',
                trim($contentType),
            ) !== 1
        ) {
            return self::error(
                415,
                'unsupported_media_type',
                'Content-Type must be application/json.',
            );
        }

        if (strlen($request->body) > self::MAX_JSON_BODY_BYTES) {
            return self::error(
                413,
                'body_too_large',
                'JSON request body exceeds the size limit.',
            );
        }

        try {
            $payload = json_decode(
                $request->body,
                false,
                8,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            return self::error(
                400,
                'invalid_json',
                'Malformed JSON request body.',
            );
        }

        if (! $payload instanceof stdClass) {
            return self::error(
                422,
                'invalid_message',
                'Expected a JSON object with a message.',
            );
        }

        $properties = get_object_vars($payload);
        $message = $properties['message'] ?? null;

        if (
            count($properties) !== 1
            || ! is_string($message)
            || trim($message) === ''
            || strlen($message) > self::MAX_MESSAGE_BYTES
            || preg_match('/[\p{Cc}\p{Cf}]/u', $message) !== 0
        ) {
            return self::error(
                422,
                'invalid_message',
                'Message must be a nonempty string of at most 120 UTF-8 bytes.',
            );
        }

        return self::json(200, [
            'message' => $message,
        ]);
    }

    private static function error(
        int $status,
        string $code,
        string $message,
    ): Response {
        return self::json($status, [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $body
     */
    private static function json(
        int $status,
        array $body,
    ): Response {
        return new Response(
            $status,
            [
                'Content-Type' => 'application/json; charset=utf-8',
                'Cache-Control' => 'no-store',
                'X-Content-Type-Options' => 'nosniff',
            ],
            json_encode(
                $body,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
            ),
        );
    }
}
