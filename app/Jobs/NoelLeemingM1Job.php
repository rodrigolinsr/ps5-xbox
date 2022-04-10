<?php
namespace App\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class NoelLeemingM1Job extends AbstractCheckPricesJob
{
    protected function getPs5Url(): string
    {
        return 'https://www.noelleeming.co.nz/c/computers-office-tech/computers/macbooks?prefn1=subclassCode&prefv1=Apple%20Macbook%20Pro';
    }

    protected function getXboxUrl(): string
    {
        return '';
    }

    protected function checkStock(string $url, float $referencePrice): bool
    {
        if (empty($url)) {
            return false;
        }

        $dom = new Dom();
        $dom->loadFromUrl($url);

        $productList = $dom->find("div[class^='product-tile']");
        
        return $productList->count() > 5;
    }
}
