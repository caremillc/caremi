<?php $this->extend("layout.app"); ?>

<?php $this->startSection("content"); ?>

<h2><?php echo htmlspecialchars($title, ENT_QUOTES, "UTF-8"); ?></h2>

<?php if ($loggedIn): ?>
    <p>Welcome back, <?php echo htmlspecialchars($user, ENT_QUOTES, "UTF-8"); ?>!</p>
<?php else: ?>
    <p>Please log in.</p>
<?php endif; ?>

<h3>User List:</h3>
<ul>
<?php foreach ($users as $u): ?>
    <li><?php echo htmlspecialchars($u, ENT_QUOTES, "UTF-8"); ?></li>
<?php endforeach; ?>
</ul>

<p>Enjoy using Careminate Framework!</p>

<?php $this->endSection(); ?>