<?php
namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class NoelLeemingJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return 'https://www.noelleeming.co.nz/c/gaming/playstation-5?srule=price-high-to-low';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.noelleeming.co.nz/c/gaming/xbox-series-xs?srule=price-high-to-low';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        if (empty($url)) {
            return false;
        }

        $dom = new Dom();
        $dom->loadFromUrl($url);

        $productList = $dom->find("div[class^='product-grid-wrapper']");

        /** @var Dom\Node\Collection $prices */
        $prices = $productList->find("span[class^='now-price']");
        /** @var Dom\Node\HtmlNode $firstPriceNode */
        $firstPriceNode = Arr::get($prices->toArray(), 0);
        $price          = floatval($firstPriceNode->getAttribute('content'));

        Log::info("Current price: $price");

        return ($price > $referencePrice);
    }
}
