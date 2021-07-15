<?php
namespace App\Jobs;

use App\Services\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class AbstractCheckPricesJob extends Job
{
    const PS5_MIN_PRICE  = 600; // Digital Edition costs $649
    const XBOX_MIN_PRICE = 700; // Not interested in Series S - Series X costs $799

    abstract protected function getPs5Url(): string;
    abstract protected function getXboxUrl(): string;
    abstract protected function checkStock(string $url, float $referencePrice);

    protected function hasPs5Stock(): bool
    {
        Log::info('Checking PS5 prices');

        return $this->checkStock($this->getPs5Url(), static::PS5_MIN_PRICE);
    }

    protected function hasXboxStock(): bool
    {
        Log::info('Checking Xbox prices');

        return $this->checkStock($this->getXboxUrl(), static::XBOX_MIN_PRICE);
    }

    /**
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     */
    public function handle(): void
    {
        Log::info('Running check prices for ' . get_called_class());

        if ($this->hasPs5Stock()) {
            $this->notify('PS5', $this->getPs5Url());
        }

        if ($this->hasXboxStock()) {
            $this->notify('Xbox', $this->getXboxUrl());
        }
    }

    protected function notify(string $product, string $ps5Url)
    {
        $cacheKey     = md5(json_encode([get_called_class(), $product, $ps5Url]));
        $sentRecently = Cache::get($cacheKey, false);

        if (!$sentRecently) {
            Mailer::notify($product, $ps5Url);
            Cache::put($cacheKey, true, Carbon::now()->addMinutes(30));
        }
    }
}
