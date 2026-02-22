<?php declare(strict_types=1);
namespace App\Http\Controllers\Dashboard;

use App\Widget\Widget;
use App\Http\Controllers\Controller;
use Careminate\Http\Responses\Response;

class DashboardController extends Controller
{
    public function __construct(private Widget $widget)
    {
    }

    public function index(): Response
    {
        return view('admin/dashboard.html.twig');
    }
}