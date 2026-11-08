<?php

declare(strict_types=1);

namespace Gene\Tests\Support;

final class Filesystem
{
    public static function tempDir(string $prefix): string
    {
        $dir = sys_get_temp_dir() . '/' . $prefix . '-' . bin2hex(random_bytes(4));
        mkdir($dir, 0o775, true);

        return $dir;
    }

    public static function remove(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            /** @var \SplFileInfo $file */
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }

    /** @param array<string, string> $files */
    public static function dump(string $dir, array $files): void
    {
        foreach ($files as $path => $content) {
            $target = $dir . '/' . $path;
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0o775, true);
            }
            file_put_contents($target, $content);
        }
    }
}
