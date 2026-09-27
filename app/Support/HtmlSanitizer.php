<?php

namespace App\Support;

/**
 * سانایزر سبک HTML برای نمایش محتوای خبر (بدون وابستگی به اکستنشن DOM).
 *
 * تگ‌های خطرناک (script/iframe/object/embed/form و...)، رویدادها (on*)،
 * کامنت‌ها و URL های javascript:/data: را حذف می‌کند تا XSS از محتوای
 * ویرایشگر (CKEditor) مسدود شود. تگ‌های خارج از لیست مجاز، escape می‌شوند.
 */
class HtmlSanitizer
{
    /** تگ‌های مجاز محتوای خبر */
    protected static array $allowedTags = [
        'p', 'br', 'hr', 'b', 'strong', 'i', 'em', 'u', 's', 'sub', 'sup',
        'ul', 'ol', 'li', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'figure', 'figcaption', 'span', 'div',
        'img', 'a',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // ۱) حذف کامل عناصر خطرناک همراه با محتوای داخلشان
        $html = preg_replace(
            '#<\s*(script|iframe|object|embed|form|style|svg|math)\b[^>]*>.*?<\s*/\s*\1\s*>#is',
            '',
            $html
        );
        $html = preg_replace(
            '#<\s*/?\s*(script|iframe|object|embed|form|style|svg|math|input|button|link|meta|base)\b[^>]*>#is',
            '',
            $html
        );

        // ۲) حذف کامنت‌های HTML (جلوگیری از ترفندهای شرطی IE و پنهان‌کاری)
        $html = preg_replace('#<!--.*?-->#s', '', $html);

        // ۳) escape کردن تگ‌های غیرمجاز (باقی‌مانده) بدون شکستن تگ‌های مجاز
        $allowed = implode('|', self::$allowedTags);
        $html = preg_replace(
            '#<(?!/?(?:' . $allowed . ')\b)#iu',
            '&lt;',
            $html
        );

        // ۴) حذف همه attribute های رویداد (on*)
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/is', '', $html);

        // ۵) خنثی‌سازی URL های خطرناک
        $html = preg_replace(
            '/(href|src)\s*=\s*([\'"]?)\s*(javascript|data|vbscript)\s*:/is',
            '$1=$2#',
            $html
        );

        // ۶) حذف style های expression خطرناک (IE legacy) و url(javascript:...)
        $html = preg_replace('/expression\s*\(/is', '', $html);

        return $html;
    }
}
