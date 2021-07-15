<?php
namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class MicrosoftStoreJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return '';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.xbox.com/en-nz/configure/8WJ714N3RBTL';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        if (empty($url)) {
            return false;
        }

        $dom = new Dom();
        $dom->loadFromUrl($url);

        /** @var Dom\Node\Collection $status */
        $status = $dom->find("button[aria-label^='Checkout bundle']");
        /** @var Dom\Node\HtmlNode $firstPriceNode */
        $firstPriceNode = Arr::get($status->toArray(), 0);
        /** @var Dom\Node\TextNode $textNode */
        $textNode = Arr::get($firstPriceNode->getChildren(), 0);
        $status   = trim($textNode->text());

        Log::info("Current status: $status");

        return strtolower($status) !== 'out of stock';
    }
}
