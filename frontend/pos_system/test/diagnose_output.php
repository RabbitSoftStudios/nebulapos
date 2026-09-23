<?php
// Script to find BOMs and whitespace before PHP tags in specific directories
// Place this in root or tests folder

$directories = [
    __DIR__ . '/../includes',
    __DIR__ . '/../views/ajax',
];

echo "Starting Scan...\n";

function scanDirRecursive($dir) {
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        checkFile($file);
    }
    
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        scanDirRecursive($subdir);
    }
}

function checkFile($path) {
    $content = file_get_contents($path);
    $filename = basename($path);
    
    // Check for BOM
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo "[CRITICAL] BOM found in: $path\n";
    }
    
    // Check for whitespace before <?php
    // We look for anything that isn't <?php starting at index 0 (ignoring BOM logic above for a sec, though usually BOM handles it)
    // If BOM exists, index 0 is occupied.
    
    $trimmed = ltrim($content);
    if (strpos($trimmed, '<?php') !== 0 && strpos($trimmed, '<?') !== 0) {
         // It might be an HTML mixed file, which is valid for views but bad for pure PHP includes
         echo "[INFO] File does not start with PHP tag immediately: $path (Might be intentional HTML)\n";
    }
    
    // Check specifically for spaces/newlines before <?php
    if (preg_match('/^\s+<\?php/', $content)) {
        echo "[CRITICAL] Whitespace detected before opening tag in: $path\n";
    }
}

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        scanDirRecursive($dir);
    } else {
        echo "Directory not found: $dir\n";
    }
}

echo "Scan Complete.\n";
