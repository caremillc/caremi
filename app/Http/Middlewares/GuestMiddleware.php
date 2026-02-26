<?php declare (strict_types = 1);

namespace App\Http\Middlewares;

use Careminate\Http\Middlewares\Contracts\MiddlewareInterface;
use Careminate\Http\Middlewares\Contracts\RequestHandlerInterface;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Redirect;
use Careminate\Http\Responses\Response;
use Careminate\Sessions\Session;
use Careminate\Sessions\SessionInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionInterface $session,
        private readonly string $redirectTo = "/admin/dashboard"
    ) {}

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();
        // Ensure session is available (StartSession middleware must run first)
        if ($this->session->has(Session::AUTH_KEY)) {
            // dd(Session::AUTH_KEY);
            // dd($this->redirectTo);
            // return new Redirect($this->redirectTo);
            // or
            return (new Redirect())->to($this->redirectTo);
        } 
        
        // else {
        //     // return new Redirect('/login');
        //     // or
        //     return (new Redirect())->to('/');
        // }

        return $handler->handle($request);
    }
}



