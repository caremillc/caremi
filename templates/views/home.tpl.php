<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
    <header>
        <h1>Welcome to the Home Page</h1>
    </header>

    <main>
        <p>This is the home page of the website. The title is passed dynamically from the controller:</p>
        <h2><?php echo htmlspecialchars($title); ?></h2>
    </main>

    <footer>
        <p>&copy; 2025 My Website</p>
    </footer>
</body>
</html>