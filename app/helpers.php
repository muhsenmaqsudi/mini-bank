<?php

if (!function_exists('to_en_number')) {
    function to_en_number(string $value): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $enNums = range(0, 9);
        $convertedPersianNums = str_replace($persian, $enNums, $value);
        return str_replace($arabic, $enNums, $convertedPersianNums);
    }
}
