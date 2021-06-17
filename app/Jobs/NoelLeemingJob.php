<?php

namespace App\Jobs;

use Illuminate\Support\Arr;
use PHPHtmlParser\Dom;

class NoelLeemingJob extends AbstractCheckPricesJob
{
    /**
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     */
    public function handle()
    {
        $ps5Url  = 'https://www.noelleeming.co.nz/shop/games-gaming/playstation/playstation-5/c11905-c2963-cplaystation5-p1.html?sorter=price-desc';
        if ($this->isPriceAbove($ps5Url, 700)) {
            $this->notify('PS5', $ps5Url);
        }

        $xboxUrl = 'https://www.noelleeming.co.nz/shop/games-gaming/xbox/xbox-series-x/c11905-cxboxone-cxboxseriesx-p1.html?sorter=price-desc';
        if ($this->isPriceAbove($xboxUrl, 700)) {
            $this->notify('Xbox', $xboxUrl);
        }
    }

    /**
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function isPriceAbove(string $url, float $referencePrice)
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

        return $price > $referencePrice;
    }
}
