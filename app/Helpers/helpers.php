<?php

if (!function_exists('number_with_delimiter')) {
    /**
     * Format a number with thousands delimiter (like Rails number_with_delimiter)
     */
    function number_with_delimiter($number, string $delimiter = ','): string
    {
        if ($number === null || $number === '') return '';
        return number_format((float)$number, 0, '.', $delimiter);
    }
}

if (!function_exists('simple_format')) {
    /**
     * Convert plain text with newlines to HTML paragraphs (like Rails simple_format)
     */
    function simple_format(?string $text): string
    {
        if (!$text) return '';
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $paragraphs = preg_split('/\n{2,}/', $text);
        $result = '';
        foreach ($paragraphs as $p) {
            $p = trim($p);
            if ($p !== '') {
                $p = nl2br($p);
                $result .= "<p>{$p}</p>\n";
            }
        }
        return $result;
    }
}

if (!function_exists('number_to_currency')) {
    /**
     * Format number as Indian Rupee currency string
     */
    function number_to_currency($amount, string $unit = '₹'): string
    {
        if ($amount === null || $amount === '') return $unit . '0.00';
        return $unit . number_format((float)$amount, 2, '.', ',');
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text to given length (like Rails truncate)
     */
    function truncate_text(?string $text, int $length = 100, string $omission = '...'): string
    {
        if (!$text) return '';
        if (mb_strlen($text) <= $length) return $text;
        return mb_substr($text, 0, $length - mb_strlen($omission)) . $omission;
    }
}
