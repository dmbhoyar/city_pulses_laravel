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

if (!function_exists('city_display_name')) {
    /**
     * Translate city names for display only. Raw DB values remain unchanged for queries/APIs.
     */
    function city_display_name($city): string
    {
        $value = is_object($city) ? (string) ($city->name ?? '') : (string) $city;
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $normalized = strtolower($value);
        $normalized = trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/', ' ', $normalized)));

        $map = [
            'washim' => 'city_washim',
            'washim main' => 'city_washim',
            'mangrulpir' => 'city_mangrulpir',
            'karanja' => 'city_karanja',
            'karanja apmc' => 'city_karanja',
            'amravati' => 'city_amravati',
            'shelubajar' => 'city_shelubajar',
            'akola' => 'city_akola',
            'pune' => 'city_pune',
            'surat' => 'city_surat',
        ];

        $uiKey = $map[$normalized] ?? null;

        if (!$uiKey) {
            return $value;
        }

        $translated = __('ui.' . $uiKey);

        return $translated !== 'ui.' . $uiKey ? $translated : $value;
    }
}
