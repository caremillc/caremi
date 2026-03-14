@extends('layout.app')

@section('content')

<h2>{{ $title }}</h2>

<form method="POST" action="/register">
    <input name="name" value="<?= old('name') ?>">
		<?php $errors = errors(); ?> <?php if(isset($errors['name'])): ?>
	<div class="error">
		<?= $errors['name'][0] ?>
	</div><?php endif; ?>
	
	
    <input name="email" value="<?= old('email') ?>">
	<?php $errors = errors(); ?> <?php if(isset($errors['email'])): ?>
	<div class="error">
		<?= $errors['email'][0] ?>
	</div><?php endif; ?>
    <input name="password" type="password">
    <button>Register</button>
</form>

@endsection