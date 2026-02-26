<?php  declare(strict_types=1); 
namespace App\Http\Controllers\Dashboard;

use App\Widget\Widget;
use App\Http\Controllers\Controller;
use Careminate\Http\Responses\Response;
use Careminate\Authentication\SessionAuthentication;

class DashboardController extends Controller
{
    public function __construct(private Widget $widget, private SessionAuthentication $auth){}

    public function index(): Response
    {
        if (! $this->auth->check()) {
            return redirect('/login');
        }
          $user = $this->auth->getUser();
         
        return view('admin/dashboard.html.twig',compact('user'));
    }
}
