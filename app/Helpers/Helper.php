<?php

namespace App\Helpers;

class Helper
{
    public static function removeSpecialCharacter($string)
    {
        if (empty($string)) {
            return $string;
        }
        $string = preg_replace('/<[\s\S]+?>/', '', $string);
        $string = trim(preg_replace('/\s+/', ' ', $string));
        return  $string;
    }
}
