<?php

namespace App\Nova\Requests;

trait PostSizeTrait
{

    /**
     * @return mixed
     */
    public static function getMaxPostSizeInKiloBytes()
    {
        $fields = ['upload_max_filesize', 'post_max_size'];
        $sizes = [];
        foreach ($fields as $field) {
            $sizes[] = self::returnKiloBytes(ini_get($field));
        }
        return min($sizes);
    }

    private static function returnKiloBytes($size_str)
    {
        $size_str = trim($size_str);

        return self::returnBytes($size_str) / 1024;
    }

    private static function returnBytes($size_str)
    {
        $size_str = trim($size_str);
        switch (substr($size_str, -1)) {
            case 'M':
            case 'm':
                return (int)$size_str * 1048576;
            case 'K':
            case 'k':
                return (int)$size_str * 1024;
            case 'G':
            case 'g':
                return (int)$size_str * 1073741824;
            default:
                return $size_str;
        }
    }
}
