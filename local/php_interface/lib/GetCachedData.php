<?php
namespace lib;
use Bitrix\Main\Data\Cache;

class GetCachedData implements InterfaceCache
{
    public $day_for_month = [];
    public $today_for_hours = [];

    function __construct($code_elem)
    {
        $cache = Cache::createInstance();

        if ($cache->initCache(self::TTL, self::day_for_month, self::init_dir, self::base_dir)) {
            $cached_data = $cache->getVars();
            $this->day_for_month = array_filter($cached_data, function ($k) use ($code_elem) {
                return $k['UF_CODE_ELEM'] == $code_elem;
            }, ARRAY_FILTER_USE_BOTH);
            echo ('CACHE DATA');
        }
        else{
            echo ('DB DATA');
            $activity = new Activity;
            $this->day_for_month = $activity->elem_day_for_month($code_elem);
        }

        if ($cache->initCache(self::TTL, self::today_for_hours, self::init_dir, self::base_dir)) {
            $cached_data = $cache->getVars();
            $this->today_for_hours = array_filter($cached_data, function ($k) use ($code_elem) {
                return $k['UF_CODE_ELEM'] == $code_elem;
            }, ARRAY_FILTER_USE_BOTH);
            echo ('CACHE DATA');
        }
        else{
            echo ('DB DATA');
            $activity = new Activity;
            $this->today_for_hours = $activity->elem_today_for_hours($code_elem);
        }
    }
}