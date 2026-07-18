<?php
/**
 * DPP Builder — packages a plugin folder into a .dpp file
 * 
 * Usage:
 *   php dpp-build.php <source-folder> [output-name.dpp]
 * 
 * If output is omitted, uses <source-folder-name>.dpp
 */

$src = $argv[1] ?? null;
if (!$src) {
    echo "Usage: php dpp-build.php <source-folder> [output-file.dpp]\n";
    echo "Example: php dpp-build.php ../test_plugin my_plugin.dpp\n";
    exit(1);
}

$src = rtrim(realpath($src), '\\/');
if (!is_dir($src)) {
    echo "Error: Source folder not found: $src\n";
    exit(1);
}

// Verify plugin.json exists
$manifestPath = $src . '/plugin.json';
if (!file_exists($manifestPath)) {
    echo "Error: No plugin.json found in source folder.\n";
    exit(1);
}

$manifest = json_decode(file_get_contents($manifestPath), true);
if (!$manifest || empty($manifest['unique_id'])) {
    echo "Error: Invalid plugin.json — missing unique_id.\n";
    exit(1);
}

$output = $argv[2] ?? (basename($src) . '.dpp');
if (!str_ends_with($output, '.dpp')) {
    $output .= '.dpp';
}

echo "Building .dpp plugin...\n";
echo "  Source:   $src\n";
echo "  Plugin:   {$manifest['name']} (v{$manifest['version']})\n";
echo "  ID:       {$manifest['unique_id']}\n";
echo "  Output:   $output\n\n";

// Collect all files recursively
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$zip = new ZipArchive;
if ($zip->open($output, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    echo "Error: Cannot create output file.\n";
    exit(1);
}

$count = 0;
foreach ($files as $file) {
    $localPath = substr($file->getPathname(), strlen($src) + 1);
    $localPath = str_replace('\\', '/', $localPath);
    $zip->addFile($file->getPathname(), $localPath);
    $count++;
    echo "  + $localPath\n";
}

$zip->close();

$size = filesize($output);
$sizeStr = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 1) . ' KB';

echo "\nDone! Packed $count files into $output ($sizeStr)\n";
echo "\nInstall: Upload via Panel → Admin → Plugins → Upload Plugin\n";
