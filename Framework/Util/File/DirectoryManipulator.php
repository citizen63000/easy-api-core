<?php

namespace EasyApiCore\Util\File;

class DirectoryManipulator
{
    /**
     * Can delete not empty directory
     */
    public static function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {

            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
    
    public static function createDirectoriesOfPath(string $path, $permissions = 0750): bool
    {
        $pathinfo = pathinfo($path);
        $directories = $pathinfo['dirname'];

        if (!is_dir($directories)) {
            mkdir($directories, $permissions, true);
            return true;
        }
        
        return true;
    }
}
