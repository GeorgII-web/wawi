<?php

namespace App\Helpers;

 class ArrayHelper
{
    public static function sortBy(&$arr, $col, $dir = SORT_ASC): void
    {
        $sortCol = [];
        foreach ($arr as $key => $row) {
            $sortCol[$key] = $row[$col];
        }
        array_multisort($sortCol, $dir, $arr);
    }
}
