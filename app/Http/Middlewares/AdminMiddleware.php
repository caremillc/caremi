<?php 
namespace App\Http\Middlewares;

use Careminate\Http\Middlewares\Contracts\MiddlewareInterface;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @param mixed $request
     * @param mixed $next
     *
     * @return mixed
     */
    public function handle($request,$next,...$role)
    {
        if (in_array('admin',$role)) {
            return redirect('admin/dashboard'); // Redirect to the admin dashboard
        }

        return $next($request);
    }
}