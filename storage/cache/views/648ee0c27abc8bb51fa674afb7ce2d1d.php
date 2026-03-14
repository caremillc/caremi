<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title ?? 'Careminate Framework', ENT_QUOTES, "UTF-8"); ?></title>
</head>
<body>

<header>
    <h1>Careminate Framework</h1>
</header>

<div class="content">
    <?php echo $this->yield("content"); ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Careminate</p>
</footer>

</body>
</html>