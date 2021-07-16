<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class JBHiFiJob extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return '';
    }

    protected function getXboxUrl(): string
    {
        return 'https://www.jbhifi.co.nz/features/gaming/xbox/xbox-series-x/';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        if (empty($url)) {
            return false;
        }

        $dom = new Dom();
        $dom->loadFromUrl($url);

        /** @var Dom\Node\Collection $prices */
        $prices = $dom->find("span[class^='amount regular']");

        /** @var Dom\Node\HtmlNode $price */
        foreach ($prices as $price) {
            $priceNumber = floatval(trim($price->text()));

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
