<h2>Register</h2>

<?php $errors = errors(); ?>

<form method="POST" action="/register">

<input name="name" value="<?= old('name') ?>">

<?php if(isset($errors['name'])): ?>
<div><?= $errors['name'][0] ?></div>
<?php endif; ?>

<input name="email" value="<?= old('email') ?>">

<?php if(isset($errors['email'])): ?>
<div><?= $errors['email'][0] ?></div>
<?php endif; ?>

<input type="password" name="password">

<button>Register</button>

</form>