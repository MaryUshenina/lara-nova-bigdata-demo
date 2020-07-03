<?php
namespace App\Cache;

interface CacheCallbackInterface{

    public static function getCachedOrRetrieve($cacheKey, callable $callback, $parameters = [],  $expires = null, $cacheGroup = null);


}
