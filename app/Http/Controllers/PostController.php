<?php

namespace App\Http\Controllers;

use Careminate\Http\Requests\Request;

class PostController extends Controller
{
    public function index()
    {
        // Show list of resources
    }

    public function create()
    {
        // Show form to create a new resource
    }

    public function store(Request $request)
    {
        // Handle storing a new resource
        var_dump($request);
    }

    public function show(Request $request,$id)
    {
        // Show a specific resource
    }

    public function edit(Request $request,$id)
    {
        // Show form to edit a resource
    }

    public function update(Request $request,$id)
    {
        // Handle updating a resource
    }

    public function destroy(Request $request,$id)
    {
        // Handle deleting a resource
    }
}
