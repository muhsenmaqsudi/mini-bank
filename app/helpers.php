<?php

if (!function_exists('to_en_number')) {
    function to_en_number(string $value): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];

        $enNums = range(0, 9);
        $convertedPersianNums = str_replace($persian, $enNums, $value);
        return str_replace($arabic, $enNums, $convertedPersianNums);
    }
}
