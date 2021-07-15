<?php

namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class NoelLeemingJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return 'https://www.noelleeming.co.nz/shop/games-gaming/playstation/playstation-5/' .
            'c11905-c2963-cplaystation5-p1.html?sorter=price-desc';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.noelleeming.co.nz/shop/games-gaming/xbox/xbox-series-x/' .
            'c11905-cxboxone-cxboxseriesx-p1.html?sorter=price-desc';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        $dom = new Dom();
        $dom->loadFromUrl($url);

        /** @var Dom\Node\Collection $prices */
        $prices = $dom->find("span[class^='price-lockup__pricing-price']");
        /** @var Dom\Node\HtmlNode $firstPriceNode */
        $firstPriceNode = Arr::get($prices->toArray(), 0);
        /** @var Dom\Node\TextNode $textNode */
        $textNode = Arr::get($firstPriceNode->getChildren(), 0);
        $price    = floatval($textNode->text());

        Log::info("Current price: $price");

        return $price > $referencePrice;
    }
}
