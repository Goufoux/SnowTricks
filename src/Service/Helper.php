<?php

namespace App\Service;

class Helper
{
    public static function slugify(string $str)
    {
        // replace non letter or digits by - 
        $str = preg_replace('~[^\pL\d]+~u', '-', $str);

        // transliterate
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);

        // remove unwanted characters
        $str = preg_replace('~[^-\w]+~', '', $str);

        // trim
        $str = trim($str, '-');

        // remove duplicate -
        $str = preg_replace('~-+~', '-', $str);

        if (empty($str)) {
            return false;
        }
        

        return $str;
    }
}