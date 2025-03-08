<?php 
namespace App\Views\Components;

use Careminate\Views\Components\Component;

class ExceptionComponent extends Component
{
    protected array $exceptionDetails;

    public function __construct(array $exceptionDetails)
    {
        $this->exceptionDetails = $exceptionDetails;
    }

    protected function renderHtml(): string
    {
        return <<<HTML
        <!doctype html>
        <html lang="en" data-bs-theme="dark">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>exception - Exception</title>
            {$this->getStyles()}
        </head>
        <body class="p-2">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-danger">An exception Occurred</h1>
                        <div class="p-2">
                            {$this->formatexceptionDetails()}
                        </div>
                        <a href="/" class="btn btn-primary">Go Home</a>
                    </div>
                </div>
            </div>
            {$this->getScripts()}
        </body>
        </html>
        HTML;
    }

    protected function getStyles(): string
    {
        return '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
    }

    protected function getScripts(): string
    {
        return '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>';
    }

    protected function formatexceptionDetails(): string
    {
        return sprintf(
            '<strong>Message:</strong> %s<br>
             <strong>File:</strong> %s<br>
             <strong>Line:</strong> %d<br>
             <strong>Trace:</strong><pre>%s</pre>',
            htmlspecialchars($this->exceptionDetails['message']),
            htmlspecialchars($this->exceptionDetails['file']),
            $this->exceptionDetails['line'],
            htmlspecialchars($this->exceptionDetails['trace'])
        );
    }
}
 