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
        return 'https://www.noelleeming.co.nz/c/gaming/playstation-5';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.noelleeming.co.nz/c/gaming/xbox-series-xs';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        if (empty($url)) {
            return false;
        }

        $dom = new Dom();
        $dom->loadFromUrl($url);

        $productList = $dom->find("li[class^='block product-list']");

        /** @var Dom\Node\Collection $prices */
        $prices = $productList->find("span[class^='price-lockup__pricing-price']");
        /** @var Dom\Node\HtmlNode $firstPriceNode */
        $firstPriceNode = Arr::get($prices->toArray(), 0);
        /** @var Dom\Node\TextNode $textNode */
        $textNode = Arr::get($firstPriceNode->getChildren(), 0);
        $price    = floatval($textNode->text());

        /** @var Dom\Node\Collection $buttons */
        $buttons = $productList->find("button");
        /** @var Dom\Node\HtmlNode $firstButton */
        $firstButton = Arr::get($buttons->toArray(), 0);
        /** @var Dom\Node\TextNode $textNode */
        $class       = $firstButton->getAttribute('class');
        $hasDisabled = Str::contains($class, 'disabled');

        Log::info("Current price: $price | Is Disabled: $hasDisabled");

        return ($price > $referencePrice) && !$hasDisabled;
    }
}
