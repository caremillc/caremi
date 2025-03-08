<?php 
namespace App\Http\Middlewares;

use Careminate\Http\Middlewares\Contracts\MiddlewareInterface;

class Middleware implements MiddlewareInterface
{
    /**
     * @param mixed $request
     * @param mixed $next
     * @param mixed ...$role
     *
     * @return mixed
     */
    public function handle($request, $next, ...$role)
    {
       
        // // Prevent redirection loop
        // if ($_SERVER['REQUEST_URI'] == '/') {
        //     return $next($request); // Skip redirect if already on the homepage
        // }
        
        // // Your condition for redirect
        // if (2 == 2) {
        //     return redirect('/');
		// 	//header('Location: '.url('/'));
		// 	//exit();
        // }
        
        return $next($request);
    }
}

