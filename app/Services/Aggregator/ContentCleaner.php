<?php

namespace App\Services\Aggregator;

use App\Models\SourceChannel;

class ContentCleaner
{
    public function clean(string $content, ?SourceChannel $source = null): array
    {
        $text = trim($content);

        $baseAds = [
            'تبلیغات','تبلیغ','ثبت سفارش','جهت رزرو','تخفیف ویژه',
            'پرداخت در محل','لینک خرید','قیمت مناسب','تخفیف استثنایی','جهت سفارش','کانال فروشگاه',
        ];

        $customKeywords = [];
        if ($source && !empty($source->filter_keywords)) {
            $parts = preg_split('/[\n,،]+/u', $source->filter_keywords);
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p !== '') $customKeywords[] = $p;
            }
        }
        $adKeywords = array_merge($baseAds, $customKeywords);

        foreach ($adKeywords as $kw) {
            if (mb_stripos($text, $kw) !== false) {
                return ['cleaned'=>$text,'is_ad'=>true,'ad_reason'=>"کلیدواژه فیلتر: {$kw}"];
            }
        }

        $removeLinks = $source ? (bool)$source->remove_links : true;
        $removeTags = $source ? (bool)$source->remove_tags : true;

        if ($removeTags) {
            $text = preg_replace('/@[\w_\.]+/u', '', $text);
            $text = preg_replace('/#[\w\x{0600}-\x{06FF}_]+/u', '', $text);
        }

        if ($removeLinks) {
            $text = preg_replace('/https?:\/\/[^\s]+/iu', '', $text);
            $text = preg_replace('/\b(t\.me|eitaa\.com|rubika\.ir|ble\.ir|splus\.ir|eitaa\.ir)\/[^\s]+/iu', '', $text);
        }

        $text = preg_replace("/\n\s*\n\s*\n+/u", "\n\n", $text);
        $text = trim($text);

        return ['cleaned'=>$text,'is_ad'=>false,'ad_reason'=>null];
    }
}
