<?php declare (strict_types = 1);
namespace App\Http\Controllers;

use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;
use Careminate\Http\Validations\Validate;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        echo'ok';
        exit;
        $search = $request->query('search');
        $page = $request->query('page', 1);

        echo "Showing page {$page} for search '{$search}'";

        die;
        return Response::success('User created', ['id' => 123]);
        return Response::error('Validation failed', ['email' => 'Email is required']);
        return Response::fromThrowable($exception);
        return response()->json(['custom' => true]); // using helper
    }

     public function about()
    {
        return 'welcome to home about page';
    }

   public function article($id, $slug = ''): string
    {
        return "Welcome to article {$id} with slug {$slug}";
    }
    
    public function store(Request $request)
    {
        $data = [
            'email'                 => 'test@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'admin',
        ];

        $rules = [
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'in:admin,user,editor',
        ];

        $validator = new Validate($data, $rules);

        if ($validator->fails()) {
            dd($validator->errors());
        }

    }
}
