<?php declare (strict_types = 1);
namespace App\Http\Controllers;

class InvokeController extends Controller
{
    public function __invoke()
    {
        return 'this is invoke controller';
    }
}