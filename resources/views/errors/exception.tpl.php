<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Application Error</title>
    <style>
        body { font-family: sans-serif; background: #f8f9fa; color: #212529; }
        .container { max-width: 800px; margin: 2rem auto; padding: 1rem; }
        .card { background: white; border-radius: 0.25rem; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .card-header { padding: 0.75rem 1.25rem; background: #dc3545; color: white; }
        .card-body { padding: 1.25rem; }
        .error-detail { margin-bottom: 1rem; }
        .trace { background: #f8f9fa; padding: 1rem; border-radius: 0.25rem; font-family: monospace; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header"><h1>Application Exception</h1></div>
            <div class="card-body">
                <div class="error-detail"><strong>Message:</strong> <?= $message ?></div>
                <div class="error-detail"><strong>File:</strong> <?= $file ?></div>
                <div class="error-detail"><strong>Line:</strong> <?= $line ?></div>
                <div class="error-detail">
                    <strong>Stack Trace:</strong>
                    <div class="trace"><?= $trace ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
