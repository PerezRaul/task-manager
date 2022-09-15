<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

use voku\helper\ASCII;

final class StrUtils
{
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    public static function snake(string $value, string $delimiter = '_'): string
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            /** @phpstan-ignore-next-line */
            $value = preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value);
            /** @phpstan-ignore-next-line */
            $value = self::lower($value);
        }

        return $value;
    }

    /**
     * Convert the given string to title case for each word.
     */
    public static function headline(string $value): string
    {
        $parts = explode(' ', $value);

        if (count($parts) <= 1) {
            $parts = self::ucsplit(implode('_', $parts));
        }

        $parts = array_map(fn(string $part) => self::title($part), $parts);

        $collapsed = str_replace(['-', '_', ' '], '_', implode('_', $parts));

        return implode(' ', array_filter(explode('_', $collapsed)));
    }

    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public static function camel(string $text): string
    {
        return lcfirst(self::studly($text));
    }

    public static function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    /**
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function ascii(string $value, string $language = ASCII::ENGLISH_LANGUAGE_CODE): string
    {
        return ASCII::to_ascii($value, $language);
    }

    /**
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function slug(
        string $title,
        string $separator = '-',
        string $language = ASCII::ENGLISH_LANGUAGE_CODE
    ): string {
        $title = StrUtils::ascii($title, $language);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

        // Replace @ with the word 'at'
        /** @phpstan-ignore-next-line */
        $title = str_replace('@', $separator . 'at' . $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', static::lower($title));

        // Replace all separator characters and whitespace by a single separator
        /** @phpstan-ignore-next-line */
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        /** @phpstan-ignore-next-line */
        return trim($title, $separator);
    }

    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    public static function contains(string $haystack, string ...$needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function startsWith(string $haystack, string ...$needles): bool
    {
        foreach ($needles as $needle) {
            if (str_starts_with($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function endsWith(string $haystack, string ...$needles): bool
    {
        foreach ($needles as $needle) {
            if (str_ends_with($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            /** @phpstan-ignore-next-line */
            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    public static function middlePad(string $start, string $end, int $padLength, string $padString): string
    {
        return str_pad($start, $padLength - strlen($end), $padString, STR_PAD_RIGHT) . $end;
    }

    public static function matches(string $start, string $end, string $string): array
    {
        preg_match_all(
            sprintf('/%s(.*?)%s/', $start, $end),
            $string,
            $matches
        );

        return array_combine($matches[0], $matches[1]);
    }

    /**
     * Split a string into pieces by uppercase characters.
     */
    public static function ucsplit(string $string): array
    {
        return preg_split('/(?=\p{Lu})/u', $string, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
