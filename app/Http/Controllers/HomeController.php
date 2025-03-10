<?php
namespace App\Http\Controllers;

use App\Models\User;
use Careminate\Http\Requests\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        foreach ($users as $user) {
            echo $user['name'] . "\n";
        }
    }
    public function store(Request $request)
    {
        // $user = new User();
        // $user->name = 'John Doe3';
        // $user->email = 'john@example.com';
        // $user->password = 'plaintext'; // Mutator hashes this
        // $user->save();

        //or 

        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret',
        ]);

        echo"<pre>";
        var_dump($user);
    }
    public function edit($id)
    {
        $user = User::find(1);
        $user->password = 'new_password'; // Mutator triggers
        $user->save(); // Saves hashed password
    }

}
