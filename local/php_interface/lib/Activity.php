<?php
namespace lib;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Service\GeoIp;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Data\Cache;

class Activity implements InterfaceCache
{
    const id_hl_block = 1;
    const id_block_products = 2;

    private $entity_data_class;
    private $list_products;

    public $curDateTime;
    private $cur2DateTime;
    private $cur_day_with_zero;
    private $one_year;

    public function __construct()
    {
        $hlblock = HL\HighloadBlockTable::getById(self::id_hl_block)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $this->entity_data_class = $entity->getDataClass();

        $this->set_list_products();
        $this->curDateTime = new DateTime();
        $this->cur2DateTime = new DateTime();
        $this->one_year = $this->cur2DateTime->add("1 year");

        $this->cur_day_with_zero = $this->curDateTime->format('d.m.Y');
    }

    public function fill_sleep_again($i, $ten_lemon)
    {
        if ($i <= $ten_lemon)
        {
            for ($y=0; $y<=1000; $y++)
            {
                $random_key = array_rand($this->list_products);
                $this->set_activity(
                    $this->list_products[$random_key]['ID'],
                    $this->list_products[$random_key]['CODE'],
                    mt_rand($this->curDateTime->getTimestamp(), $this->one_year->getTimestamp())
                );
            $i++;
            }
        }
        echo json_encode($i);
    }

    public function set_activity($id_item, $code_item, $date_time = null)
    {
        $data = array(
            "UF_IP"=> GeoIp\Manager::getRealIp(),
            "UF_ID_ELEM"=> $id_item,
            "UF_CODE_ELEM"=> $code_item,
            "UF_DATE_TIME"=> !empty($date_time) ? DateTime::createFromTimestamp($date_time) : new DateTime()
        );
        $this->entity_data_class::add($data);
    }
    private function set_list_products()
    {
        $this->list_products = ElementTable::getList([
            'filter' => ['IBLOCK_ID' => self::id_block_products, 'ACTIVE' => 'Y'],
            'select' => ['ID', 'CODE']
        ])->fetchAll();
    }

    public function elem_today_for_hours(string $code_elem = null): array
    {
        $next_day_with_zero = $this->curDateTime->add("1 day")->format('d.m.Y');

        $rsData = $this->entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array(),
            "filter" => array(
                $this->get_filter_for_code($code_elem),
                '>=UF_DATE_TIME' => DateTime::createFromUserTime($this->cur_day_with_zero),
                '<=UF_DATE_TIME' => DateTime::createFromUserTime($next_day_with_zero),
            ),
//            "limit" => 500,
        ));

        $res = [];
        while($arData = $rsData->Fetch()){
            $res[] = [
                'ID' => $arData['ID'],
                'UF_CODE_ELEM' => $arData['UF_CODE_ELEM'],
                'UF_DATE_TIME' => $arData['UF_DATE_TIME']->toString(),
                'FORMATTED_TEXT' => $arData['UF_DATE_TIME']->format('H').' часов, посещение товара'.$arData['UF_CODE_ELEM'].' пользователем '.$arData['UF_IP'],
                'FLAG_SORT' => (int)$arData['UF_DATE_TIME']->format('H')
            ];
        }
        usort($res, function($a,$b){
            return ($a['FLAG_SORT']-$b['FLAG_SORT']);
        });

        if (empty($code_elem))
            $this->writeCache(self::today_for_hours, $res);

        return $res;

    }
    public function elem_day_for_month(string $code_elem = null): array
    {
        $next_month_with_zero = $this->curDateTime->add("1 month")->format('d.m.Y');

        $rsData = $this->entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array(),
            "filter" => array(
                $this->get_filter_for_code($code_elem),
                '>=UF_DATE_TIME' => DateTime::createFromUserTime($this->cur_day_with_zero),
                '<=UF_DATE_TIME' => DateTime::createFromUserTime($next_month_with_zero),
            ),
//            "limit" => 500,
        ));

        $res = [];
        while($arData = $rsData->Fetch()){
            $res[] = [
                'ID' => $arData['ID'],
                'UF_CODE_ELEM' => $arData['UF_CODE_ELEM'],
                'UF_DATE_TIME' => $arData['UF_DATE_TIME']->toString(),
                'FORMATTED_TEXT' => $arData['UF_DATE_TIME']->format('d.m.Y').', посещение товара '.$arData['UF_CODE_ELEM'].' пользователем '.$arData['UF_IP'],
                'FLAG_SORT' => (int)$arData['UF_DATE_TIME']->format('d')
            ];
        }
        usort($res, function($a,$b){
            return ($a['FLAG_SORT']-$b['FLAG_SORT']);
        });

        if (empty($code_elem))
            $this->writeCache(self::day_for_month, $res);

        return $res;
    }
    private function get_filter_for_code($code_elem)
    {
        return (!empty($code_elem)) ? ['UF_CODE_ELEM' => $code_elem] : '';
    }

    public function set_cached_data(): void
    {
        if (!$this->checkCache(self::day_for_month))
            $this->elem_day_for_month();
        if (!$this->checkCache(self::today_for_hours))
            $this->elem_today_for_hours();
    }

    private function writeCache(string $uniqueString, array $data): void
    {
        $cache = Cache::createInstance(); // получаем экземпляр класса
        if (!$cache->initCache(self::TTL, $uniqueString, self::init_dir, self::base_dir)) {
            if ($cache->startDataCache()) {
                $cache->endDataCache($data); // записываем в кеш
            }
        }
    }
    private function checkCache(string $uniqueString): bool
    {
        $cache = Cache::createInstance(); // получаем экземпляр класса
        return $cache->initCache(self::TTL, $uniqueString, self::init_dir, self::base_dir);
    }
    private function clearCache(string $uniqueString): bool
    {
        $cache = Cache::createInstance();
        return $cache->clean($uniqueString,self::init_dir, self::base_dir);
    }
}


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$_DATA = json_decode(file_get_contents('php://input'), true);

if (!empty($_DATA)) {
    $wat = new Activity();
    $wat->fill_sleep_again($_DATA['i'], $_DATA['ten_lemon']);
}