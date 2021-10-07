<?php
namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class TheWarehouseJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return 'https://www.thewarehouse.co.nz/p/ps5-console-%28strictly-1-unit-per-customer%29/R2695122.html';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.thewarehouse.co.nz/p/xbox-series-x-1tb-console/R2708605.html';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        $dom = new Dom();
        $dom->loadFromUrl($url);

        /** @var Dom\Node\Collection $status */
        $status = $dom->find("div[class^='alert-body']");
        /** @var Dom\Node\HtmlNode $firstPriceNode */
        $firstPriceNode = Arr::get($status->toArray(), 0);

        if ($firstPriceNode) {
            /** @var Dom\Node\TextNode $textNode */
            $textNode   = Arr::get($firstPriceNode->getChildren(), 0);
            $statusText = trim($textNode->text());

            Log::info("Current status: $statusText");

            $toLowerStatus = strtolower($status);

            return $toLowerStatus !== 'out of stock'
                && $toLowerStatus !== 'in-store only';
        }

        return false;
    }
}
