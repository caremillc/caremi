<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = request();
    }

    protected function response(
        string $content,
        int $status = 200,
        array $headers = []
    ): Response
    {
        return new Response($content, $status, $headers);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return new Response(
            json_encode($data),
            $status,
            ['Content-Type' => 'application/json']
        );
    }

    protected function input(string $key, $default = null)
    {
        return $this->request->input($key, $default);
    }
}
