<?php
namespace App\Jobs;

use App\Services\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

abstract class AbstractCheckPricesJob extends Job
{
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