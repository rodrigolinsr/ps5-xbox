<?php
namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class PBTechJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return 'https://www.pbtech.co.nz/category/gaming/gaming-consoles/playstation?o=highest_price';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.pbtech.co.nz/category/gaming/gaming-consoles/xbox?o=highest_price';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        $dom = new Dom();
        $dom->loadFromUrl($url);

        /** @var Dom\Node\Collection $prices */
        $prices = $dom->find("span[class^='price_full']");

        /** @var Dom\Node\HtmlNode $price */
        foreach ($prices as $price) {
            $priceText   = trim($price->text());
            $priceNumber = floatval(str_replace('$', '', $priceText));

            if (empty($priceNumber)) {
                continue;
            }

            Log::info("Price: $priceNumber");

            if ($priceNumber > $referencePrice) {
                return true;
            }
        }

        return false;
    }
}
