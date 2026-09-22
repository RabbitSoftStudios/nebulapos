<?php
echo "Starting debug script...\n";
$autoload = __DIR__ . '/../../../../../vendor/autoload.php';
echo "Looking for autoload at: $autoload\n";
if (!file_exists($autoload)) {
    die("Autoload file NOT FOUND at $autoload\n");
}
require_once $autoload;
echo "Autoload included.\n";

echo "Checking Endroid\QrCode\QrCode...\n";

if (class_exists('Endroid\QrCode\QrCode')) {
    echo "Class Endroid\QrCode\QrCode exists.\n";
    $rc = new ReflectionClass('Endroid\QrCode\QrCode');
    if ($rc->hasMethod('create')) {
        echo "Method create() exists.\n";
    } else {
        echo "Method create() DOES NOT exist.\n";
    }
    
    echo "Constructors:\n";
    $constructor = $rc->getConstructor();
    if ($constructor) {
        foreach ($constructor->getParameters() as $param) {
            echo " - " . $param->getName() . "\n";
        }
    } else {
        echo "No constructor.\n";
    }
} else {
    echo "Class Endroid\QrCode\QrCode NOT found.\n";
}

if (class_exists('Endroid\QrCode\Builder\Builder')) {
    echo "Class Endroid\QrCode\Builder\Builder exists.\n";
} else {
    echo "Class Endroid\QrCode\Builder\Builder NOT found.\n";
}
