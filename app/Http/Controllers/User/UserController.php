<?php declare (strict_types = 1);
namespace App\Http\Controllers\User;

use App\Models\User;
use Careminate\Support\Str;
use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use Careminate\Filesystem\FileRequest;


class UserController extends Controller 
{
    public function index()
    {
        Str::camel('user_name');        // "userName"
        Str::studly('user_name');       // "UserName"
        Str::snake('UserName');         // "user_name"
        Str::uuid();                    // Random UUID
        Str::slug('Hello World!');      // "hello-world"
        Str::limit('This is a long sentence', 10); // "This is a..."

        
    }

    public function store(Request $request)
    {
        $file = FileRequest::file('avatar');

    if ($file && $file->isValid()) {
        $path = $file->store(storage_path('uploads/avatars'));

        // $path now contains the full path to the stored file
    }

        $name = Request::files('name');
        $file = FileRequest::file('avatar');

        if (FileRequest::hasFile('avatar')) {
            FileRequest::move('avatar', storage_path('uploads/avatars'));
        }


        if ($file && $file->isValid() && $file->isImage()) {
        $file->store('uploads/avatars');
        }

        if (!$file->isMime(['image/jpeg', 'image/png'])) {
            // return an error: "Only JPEG and PNG allowed"
        }


        if ($file->isImage() && $file->hasExtension(['jpg', 'png'])) {
            $file->store('uploads/images');
        }



        $file = FileRequest::file('resume');

        if ($file && $file->isValid()) {
            $path = $file->storeAs('uploads/resumes', 'john-doe.pdf');
            // returns 'uploads/resumes/john-doe.pdf' or false
        }

    }

    // public function show(User $user)
    // {
    //     // $user is already a resolved model
    //     return view('users.profile', compact('user'));
    // }
}
