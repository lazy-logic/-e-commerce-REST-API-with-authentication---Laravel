<?php





$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Could not resolve project root.\n");
    exit(1);
}

$skipDirs = [
    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR,
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $path = $fileInfo->getPathname();

    
    if (substr($path, -4) !== '.php') {
        continue;
    }

    
    $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $skip = false;
    foreach ($skipDirs as $skipDir) {
        if (strpos($normalized, $skipDir) !== false) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    $code = file_get_contents($path);
    if ($code === false) {
        fwrite(STDERR, "Failed to read: {$path}\n");
        continue;
    }

    $tokens = token_get_all($code);
    $output = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            [$id, $text] = $token;

            
            if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
                continue;
            }

            $output .= $text;
        } else {
            $output .= $token;
        }
    }

    if ($output !== $code) {
        file_put_contents($path, $output);
        echo "Stripped comments from: {$path}\n";
    }
}

echo "Done removing PHP comments.\n";
