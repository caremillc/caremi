<?php declare(strict_types=1);
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller 
{
    public function index()
    {
        return 'admin dashboard';
    }
}