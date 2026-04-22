<?php
if (!function_exists('mb_internal_encoding')) {
    function mb_internal_encoding(?string $encoding = null)
    {
        static $internalEncoding = 'UTF-8';
        if ($encoding !== null) {
            $internalEncoding = $encoding;
            return true;
        }
        return $internalEncoding;
    }
}

if (!function_exists('mb_regex_encoding')) {
    function mb_regex_encoding(?string $encoding = null)
    {
        static $regexEncoding = 'UTF-8';
        if ($encoding !== null) {
            $regexEncoding = $encoding;
            return true;
        }
        return $regexEncoding;
    }
}

if (!function_exists('mb_detect_order')) {
    function mb_detect_order(?array $encodings = null)
    {
        static $order = ['UTF-8'];
        if ($encodings !== null) {
            $order = $encodings;
            return true;
        }
        return $order;
    }
}

if (!function_exists('mb_substitute_character')) {
    function mb_substitute_character($substitute = null)
    {
        static $character = 0x3F;
        if ($substitute !== null) {
            $character = $substitute;
            return true;
        }
        return $character;
    }
}

if (!function_exists('mb_list_encodings')) {
    function mb_list_encodings(): array
    {
        return ['UTF-8', 'ASCII', 'ISO-8859-1'];
    }
}

if (!function_exists('mb_detect_encoding')) {
    function mb_detect_encoding(string $string, $encodings = null, bool $strict = false)
    {
        return preg_match('//u', $string) ? 'UTF-8' : 'ISO-8859-1';
    }
}

if (!function_exists('mb_convert_encoding')) {
    function mb_convert_encoding(string $string, string $to_encoding, $from_encoding = null): string
    {
        if (function_exists('iconv')) {
            $from = is_array($from_encoding) ? ($from_encoding[0] ?? 'UTF-8') : ($from_encoding ?: 'UTF-8');
            $converted = @iconv($from, $to_encoding . '//IGNORE', $string);
            if ($converted !== false) {
                return $converted;
            }
        }
        return $string;
    }
}

if (!function_exists('mb_encode_numericentity')) {
    function mb_encode_numericentity(string $string, array $convmap, ?string $encoding = null, bool $hex = false): string
    {
        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) {
            return $string;
        }
        $out = '';
        foreach ($chars as $char) {
            $code = mb_ord($char, $encoding ?: 'UTF-8');
            $out .= '&#' . $code . ';';
        }
        return $out;
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen(string $string, ?string $encoding = null): int
    {
        preg_match_all('/./us', $string, $matches);
        return count($matches[0]);
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr(string $string, int $start, ?int $length = null, ?string $encoding = null): string
    {
        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) {
            return '';
        }
        if ($start < 0) {
            $start = count($chars) + $start;
        }
        $slice = $length === null ? array_slice($chars, $start) : array_slice($chars, $start, $length);
        return implode('', $slice);
    }
}

if (!function_exists('mb_substr_count')) {
    function mb_substr_count(string $haystack, string $needle, ?string $encoding = null): int
    {
        return substr_count($haystack, $needle);
    }
}

if (!function_exists('mb_strpos')) {
    function mb_strpos(string $haystack, string $needle, int $offset = 0, ?string $encoding = null)
    {
        $result = strpos($haystack, $needle, $offset);
        return $result === false ? false : $result;
    }
}

if (!function_exists('mb_stripos')) {
    function mb_stripos(string $haystack, string $needle, int $offset = 0, ?string $encoding = null)
    {
        $result = stripos($haystack, $needle, $offset);
        return $result === false ? false : $result;
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $string, ?string $encoding = null): string
    {
        return strtolower($string);
    }
}

if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper(string $string, ?string $encoding = null): string
    {
        return strtoupper($string);
    }
}

if (!function_exists('mb_convert_case')) {
    function mb_convert_case(string $string, int $mode, ?string $encoding = null): string
    {
        if (defined('MB_CASE_UPPER') && $mode === MB_CASE_UPPER) return mb_strtoupper($string, $encoding);
        if (defined('MB_CASE_LOWER') && $mode === MB_CASE_LOWER) return mb_strtolower($string, $encoding);
        return ucwords(mb_strtolower($string, $encoding));
    }
}

if (!function_exists('mb_ucwords')) {
    function mb_ucwords(string $string, ?string $encoding = null): string
    {
        return ucwords(mb_strtolower($string, $encoding));
    }
}

if (!function_exists('mb_ord')) {
    function mb_ord(string $string, ?string $encoding = null): int
    {
        $u = unpack('N', mb_convert_encoding($string, 'UCS-4BE', $encoding ?: 'UTF-8'));
        return $u[1];
    }
}

if (!function_exists('mb_chr')) {
    function mb_chr(int $codepoint, ?string $encoding = null): string
    {
        return mb_convert_encoding(pack('N', $codepoint), $encoding ?: 'UTF-8', 'UCS-4BE');
    }
}

if (!function_exists('mb_str_split')) {
    function mb_str_split(string $string, int $length = 1, ?string $encoding = null): array
    {
        if ($length < 1) {
            throw new ValueError('mb_str_split(): Argument #2 ($length) must be greater than 0');
        }
        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) {
            return [];
        }
        return array_map('implode', array_chunk($chars, $length));
    }
}
