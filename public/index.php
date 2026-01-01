<?php declare(strict_types=1);

use Careminate\Http\Requests\Request;

// ---------------------------------------------------------
// Bootstrap the framework
// ---------------------------------------------------------
require __DIR__ . '/../bootstrap/app.php';

// Create Request from globals
$request = Request::createFromGlobals();

// ============================
// Display Request Info
// ============================
echo"<pre>";
echo "<h2>Request Info</h2>";
echo "<strong>Method:</strong> " . $request->getMethod() . "<br>";
echo "<strong>Path:</strong> " . $request->getPathInfo() . "<br>";
echo "<strong>Full URL:</strong> " . $request->fullUrl() . "<br>";
echo "<strong>Is Secure:</strong> " . ($request->isSecure() ? 'Yes' : 'No') . "<br>";
echo "<strong>Client IP:</strong> " . $request->ip() . "<br>";
echo "<strong>User Agent:</strong> " . $request->userAgent() . "<br>";

die();
// ============================
// Test GET/POST Parameters
// ============================
echo "<h2>Input Data</h2>";
echo "<strong>GET param 'id':</strong> " . $request->query('id', 'N/A') . "<br>";
echo "<strong>POST param 'name':</strong> " . $request->post('name', 'N/A') . "<br>";
echo "<strong>Any input 'data':</strong> " . $request->input('data', 'N/A') . "<br>";

// ============================
// Test JSON Input
// ============================
if ($request->isJson()) {
    echo "<h2>JSON Input</h2>";
    $jsonData = $request->json();
    echo "<pre>" . json_encode($jsonData, JSON_PRETTY_PRINT) . "</pre>";
}

// ============================
// Test Headers
// ============================
echo "<h2>Headers</h2>";
foreach ($request->getHeaders() as $name => $value) {
    echo "<strong>{$name}:</strong> {$value}<br>";
}

// ============================
// Test Only & Except
// ============================
echo "<h2>Filtered Input</h2>";
$only = $request->only(['id', 'name']);
$except = $request->except(['password']);
echo "<strong>Only 'id' & 'name':</strong> <pre>" . print_r($only, true) . "</pre>";
echo "<strong>Except 'password':</strong> <pre>" . print_r($except, true) . "</pre>";

// ============================
// Test Files
// ============================
echo "<h2>Files</h2>";
foreach ($request->allFiles() as $key => $file) {
    echo "<strong>{$key}:</strong> ";
    echo $request->hasFile($key) ? "Uploaded ({$file['tmp_name']})" : "Not uploaded";
    echo "<br>";
}

// ============================
// Test Spoofed Methods (PUT/PATCH/DELETE via POST)
// ============================
echo "<h2>Method Checks</h2>";
echo "isPost(): " . ($request->isPost() ? 'Yes' : 'No') . "<br>";
echo "isGet(): " . ($request->isGet() ? 'Yes' : 'No') . "<br>";
echo "isPut(): " . ($request->isPut() ? 'Yes' : 'No') . "<br>";
echo "isPatch(): " . ($request->isPatch() ? 'Yes' : 'No') . "<br>";
echo "isDelete(): " . ($request->isDelete() ? 'Yes' : 'No') . "<br>";



