<?php

namespace app\utils;

class ReplaceUtils {

    // 同等数量替换成*
    public static function replace($string) {
        $newString = mb_substr($string, 0, 1, 'utf-8');

        preg_match_all('/./us', $string, $match);

        for ($i = 1; $i <  count($match[0]); $i++) {
            $newString = $newString.'*';
        }

        return $newString;
    }
}