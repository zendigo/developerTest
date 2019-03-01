<?
use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Data\Cache;

class CCachedIblockElementList
{

    const DEF_CACHE_TIME = 3600;  //Стандартное время кэширования
    /*Все параметры для GetList как у стандартного CIBlockElement::GetList*/
    public static function GetList($arOrder = array("SORT" => "ASC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = [], $iCacheTime = 0)
    {

        $arResult = array();

        $sClassName = str_replace('\\', '/', __CLASS__);

        $sCachePath = '/'.$sClassName.'/'.__FUNCTION__.'/';

        $arCacheId = array(
            $sCachePath,
            $arOrder,
            $arFilter,
            $arGroupBy,
            $arNavStartParams,
            $arSelectFields,
        );

        $sCacheId = serialize($arCacheId);

        if ($iCacheTime <= 0) {
            $iCacheTime = self::DEF_CACHE_TIME;
        }

        $obCache = Cache::createInstance();
        if ($obCache->initCache($iCacheTime, $sCacheId, $sCachePath)) {
            $arResult = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {

            Loader::includeModule("iblock");
            $obTaggedCache = Application::getInstance()->getTaggedCache();
            $obTaggedCache->startTagCache($sCachePath);
            if (!empty($arFilter['IBLOCK_ID'])) {
                if (is_array($arFilter['IBLOCK_ID'])) {
                    foreach ($arFilter['IBLOCK_ID'] as $iIblockId) {
                        $obTaggedCache->registerTag('iblock_id_'.$iIblockId);
                    }
                } else {
                    $obTaggedCache->registerTag('iblock_id_'.$arFilter['IBLOCK_ID']);
                }
            }

            $obRes = \CIBlockElement::GetList(
                $arOrder,
                $arFilter,
                $arGroupBy,
                $arNavStartParams,
                $arSelectFields
            );
            while ($arElement = $obRes->GetNext()) {
                $arResult[] = $arElement;
            }

            $obTaggedCache->endTagCache();
            $obCache->endDataCache($arResult);
        }

        return $arResult;
    }

}
