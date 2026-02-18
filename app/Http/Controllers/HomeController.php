<?php declare(strict_types=1);
namespace App\Http\Controllers;

use App\Widget\Widget;
use Careminate\Http\Responses\Response;

class HomeController extends Controller
{
    public function __construct(private Widget $widget)
    {
    }

    public function index(): Response
    {
        $template = "<h1>Hello {{ name }}</h1>";

        return $this->render($template, [
            'name' => $this->widget->name
        ]);
    }
}