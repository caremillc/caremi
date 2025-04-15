<?php 
namespace App\Http\Controllers\Post;

use Careminate\Sessions\Session;
use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index');
    }
    public function create()
    {
        return view('posts.create');
    }
  
    
    public function store(Request $request)
{
    error_log('Form submitted!'); // Check PHP error log
    var_dump($request->all()); // Output request data
    die('Debugging point'); // Stop execution

    
    if (!$request->verifyCsrf()) {
        Session::flash('errors', ['CSRF token mismatch']);
        return Response::back();
    }

    $validation = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string'
    ]);

    if ($validation->failed()) {
        return Response::back()->withErrors($validation);
    }

    // Save logic here
    return Response::redirect('/admin/posts');
}

    public function show($id)
    {
        return redirect('posts');
    }
    public function edit($id)
    {
        return redirect('posts');
    }
    public function update($id)
    {
        return redirect('posts');
    }
    public function destroy($id)
    {
        return redirect('posts');
    }
}
