<?php declare(strict_types=1);

// Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Careminate Framework Debug</h1>";

// ============================
// Step 1: Check vendor autoload
// ============================
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
echo "<h2>Step 1: Checking Autoload</h2>";
echo "Autoload path: $autoload<br>";

if (!file_exists($autoload)) {
    die("❌ ERROR: vendor/autoload.php not found. Run 'composer install'<br>");
}

echo "✓ vendor/autoload.php exists<br>";

require_once $autoload;
echo "✓ Autoloader loaded<br>";

// ============================
// Step 2: Check Application class
// ============================
echo "<h2>Step 2: Checking Classes</h2>";

if (class_exists('Careminate\Foundation\Application')) {
    echo "✓ Careminate\\Foundation\\Application class found<br>";
} else {
    echo "❌ Careminate\\Foundation\\Application class NOT found<br>";
    
    // Try to include manually
    $appFile = dirname(__DIR__) . '/framework/src/Foundation/Application.php';
    echo "Trying to include: $appFile<br>";
    
    if (file_exists($appFile)) {
        require_once $appFile;
        echo "✓ Application.php included manually<br>";
        
        if (class_exists('Careminate\Foundation\Application')) {
            echo "✓ Application class now exists!<br>";
        } else {
            echo "❌ Still not found after manual include<br>";
        }
    } else {
        echo "❌ Application.php file doesn't exist!<br>";
    }
}

// ============================
// Step 3: List all classes
// ============================
echo "<h2>Step 3: All Loaded Classes</h2>";
$careminateClasses = [];
foreach (get_declared_classes() as $class) {
    if (str_starts_with($class, 'Careminate\\')) {
        $careminateClasses[] = $class;
    }
}

if (empty($careminateClasses)) {
    echo "No Careminate classes loaded!<br>";
} else {
    echo "Found " . count($careminateClasses) . " Careminate classes:<br>";
    echo "<pre>";
    foreach ($careminateClasses as $class) {
        echo "- $class\n";
    }
    echo "</pre>";
}

// ============================
// Step 4: Check Composer Autoload
// ============================
echo "<h2>Step 4: Composer Autoload Info</h2>";
if (function_exists('spl_autoload_functions')) {
    $autoloaders = spl_autoload_functions();
    echo "Number of autoloaders: " . count($autoloaders) . "<br>";
    
    foreach ($autoloaders as $index => $autoloader) {
        echo "Autoloader #$index: " . gettype($autoloader) . "<br>";
    }
}

// ============================
// Step 5: Try to bootstrap
// ============================
echo "<h2>Step 5: Trying to Bootstrap</h2>";
try {
    $bootstrapFile = dirname(__DIR__) . '/bootstrap/app.php';
    echo "Loading bootstrap: $bootstrapFile<br>";
    
    if (!file_exists($bootstrapFile)) {
        die("❌ Bootstrap file not found!");
    }
    
    $app = require $bootstrapFile;
    echo "✓ Bootstrap loaded<br>";
    
    if (is_object($app)) {
        echo "✓ Application object created: " . get_class($app) . "<br>";
        
        // Test Request
        if (class_exists('Careminate\Http\Requests\Request')) {
            $request = \Careminate\Http\Requests\Request::createFromGlobals();
            echo "✓ Request object created<br>";
            
            // Test the application
            echo "<h2>Step 6: Testing Application</h2>";
            $app->handle($request);
        } else {
            echo "❌ Request class not found<br>";
        }
    } else {
        echo "❌ Bootstrap didn't return an Application object<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Bootstrap Error: " . $e->getMessage() . "<br>";
    echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
}


