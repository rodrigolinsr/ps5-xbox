<?php
namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class MightyApeJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return 'https://www.mightyape.co.nz/product/sony-playstation-5-console/31675007';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.mightyape.co.nz/product/xbox-series-x-console/30472387';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        $dom = new Dom();
        $dom->loadFromUrl($url);

        /** @var Dom\Node\Collection $status */
        $status = $dom->find("div[class^='status']");
        /** @var Dom\Node\HtmlNode $firstPriceNode */
        $firstPriceNode = Arr::get($status->toArray(), 0);
        /** @var Dom\Node\TextNode $textNode */
        $textNode = Arr::get($firstPriceNode->getChildren(), 0);
        $status   = trim($textNode->text());

        Log::info("Current status: $status");

        return $status !== 'Unavailable';
    }
}
