<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
    <style>
        body { font-family: sans-serif; background: #fefefe; text-align: center; padding: 10%; }
        h1 { font-size: 3em; color: #d33; }
        p { color: #777; }
    </style>
</head>
<body>
    <h1>403</h1>
    <p><?= $message ?? 'You are not authorized to access this resource.' ?></p>
</body>
</html>
