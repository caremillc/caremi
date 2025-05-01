<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Careminate\Http\Requests\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return $this->response('Welcome to Home Page');
    }
}