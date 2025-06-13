<!DOCTYPE html>
<html>
<head>
    <title>500 Server Error</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; text-align: center; padding: 10%; }
        h1 { font-size: 3em; color: #c00; }
        p { color: #555; }
    </style>
</head>
<body>
    <h1>500</h1>
    <p><?= $message ?? 'Something went wrong on our end.' ?></p>
</body>
</html>
