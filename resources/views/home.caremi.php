@extends('layout.app')

@section('content')

<h2>{{ $title }}</h2>

@if($loggedIn)
    <p>Welcome back, {{ $user }}!</p>
@else
    <p>Please log in.</p>
@endif

<h3>User List:</h3>
<ul>
@foreach($users as $u)
    <li>{{ $u }}</li>
@endforeach
</ul>

<p>Enjoy using Careminate Framework!</p>

@endsection