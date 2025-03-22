<?php 
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
        $content = "<h1>Hello {$this->widget->name} from HomeController</h1>";

        return new Response($content);
    }
}