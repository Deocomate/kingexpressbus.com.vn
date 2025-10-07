<?php

$scanDirectories = [
    __DIR__ . '/app',
    __DIR__ . '/config',
    __DIR__ . '/database',
    __DIR__ . '/resources',
    __DIR__ . '/routes',
];

$allowedExtensions = [
    'php',
    'js',
    'json',
    'css',
    'scss',
    'html',
    'md',
    'txt',
    'env',
];

$colorRed = "\033[31m";
$colorGreen = "\033[32m";
$colorYellow = "\033[33m";
$colorReset = "\033[0m";

echo "This script will recursively scan specified directories and remove UTF-8 BOM from files.\n";
echo "Directories to be scanned:\n";
foreach ($scanDirectories as $dir) {
    echo "- " . str_replace(__DIR__ . '/', '', $dir) . "\n";
}
echo "Allowed file extensions: " . implode(', ', $allowedExtensions) . "\n\n";

if (php_sapi_name() === 'cli') {
    $handle = fopen('php://stdin', 'r');
    echo $colorYellow . "Are you sure you want to proceed? (y/n): " . $colorReset;
    $line = strtolower(trim(fgets($handle)));
    if ($line !== 'y') {
        echo $colorRed . "Operation cancelled by user.\n" . $colorReset;
        exit;
    }
    fclose($handle);
}

echo "\nStarting BOM removal process...\n";

$scannedFiles = 0;
$fixedFiles = 0;

foreach ($scanDirectories as $directory) {
    if (!is_dir($directory)) {
        fwrite(STDERR, $colorRed . "Skipping missing directory: {$directory}\n" . $colorReset);
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $directory,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
        )
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile()) {
            continue;
        }

        $extension = strtolower($fileInfo->getExtension());
        if (!in_array($extension, $allowedExtensions)) {
            continue;
        }

        $path = $fileInfo->getPathname();
        $scannedFiles++;

        $contents = @file_get_contents($path);

        if ($contents === false) {
            fwrite(STDERR, $colorRed . "Unable to read file: {$path}\n" . $colorReset);
            continue;
        }

        if (strncmp($contents, "\xEF\xBB\xBF", 3) === 0) {
            if (@file_put_contents($path, substr($contents, 3)) === false) {
                fwrite(STDERR, $colorRed . "Unable to write file: {$path}\n" . $colorReset);
            } else {
                echo $colorGreen . "Removed BOM from: " . str_replace(__DIR__ . '/', '', $path) . "\n" . $colorReset;
                $fixedFiles++;
            }
        }
    }
}

echo "\n" . $colorGreen . "Process completed." . $colorReset . "\n";
echo "Scanned files: " . $colorYellow . $scannedFiles . $colorReset . "\n";
echo "Files with BOM fixed: " . $colorYellow . $fixedFiles . $colorReset . "\n";
