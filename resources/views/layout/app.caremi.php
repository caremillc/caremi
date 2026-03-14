<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Careminate Framework' }}</title>
</head>
<body>

<header>
    <h1>Careminate Framework</h1>
</header>

<div class="content">
    @yield('content')
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Careminate</p>
</footer>

</body>
</html>