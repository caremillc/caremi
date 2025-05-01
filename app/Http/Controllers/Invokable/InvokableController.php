<?php declare(strict_types=1);
namespace App\Http\Controllers\Invokable;

use App\Http\Controllers\Controller;
use Careminate\Http\Responses\Response;

class InvokableController extends Controller 
{
    public function __invoke()
    {
        return new Response("Invokable controller executed!");
    }
}