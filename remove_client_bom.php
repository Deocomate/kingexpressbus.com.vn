<?php
$directories = [
    __DIR__ . '/app',
    __DIR__ . '/database',
    __DIR__ . '/resources',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        fwrite(STDERR, "Skipping missing directory: {$directory}\n");
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

        $path = $fileInfo->getPathname();
        $contents = @file_get_contents($path);

        if ($contents === false) {
            fwrite(STDERR, "Unable to read file: {$path}\n");
            continue;
        }

        if (strncmp($contents, "\xEF\xBB\xBF", 3) === 0) {
            if (file_put_contents($path, substr($contents, 3)) === false) {
                fwrite(STDERR, "Unable to write file: {$path}\n");
                continue;
            }

            echo "Removed BOM from {$path}\n";
        }
    }
}
