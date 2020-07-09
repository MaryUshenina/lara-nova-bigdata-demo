<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;


trait CacheCallbackTrait
{

    public static function getCachedOrRetrieve($cacheKey, callable $callback, $parameters = [], $expires = null, $cacheGroup = null)
    {
        if(!config('cache.callback_trait', true)){
            return $callback($parameters);
        }

        $cacheGroup = $cacheGroup ?? self::class;
        $cacheKey =  $cacheGroup.'_'.$cacheKey;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $data = $callback($parameters);

        Cache::put($cacheKey, $data, $expires);

        return $data;
    }


}
