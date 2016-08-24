<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Config\Option;

//$path = $this->GetPath();

// $arParams

$arResult["ITEMS"] = array();

list($directoryValues, $dirAliases, $directoryGroups, $sectionsPaths) = \Rodzeta\Referenceattribs\Utils::get();

$arResult["USE_OPTIONS_LINKS"] = Option::get("rodzeta.referenceattribs", "use_options_links");
$arResult["CURRENT_SECTION_URL"] = $APPLICATION->GetCurPage(false);
$arResult["CURRENT_SECTION_ID"] = null;
$arResult["SELECTED_VALUES"] = array();
$GLOBALS["RODZETA_CATALOG_FILTER"] = array();

$currentUrlSegments = array_flip(array_filter(explode("/", $arResult["CURRENT_SECTION_URL"])));
foreach ($directoryGroups as $group => $values) {
	$groupName = $directoryValues[$group]["NAME"];
	$arResult["ITEMS"][$groupName] = array(
		"GROUP" => &$directoryValues[$group],
		"VALUE" => null,
	);
	foreach ($values as $id => $v) {
    $arResult["ITEMS"][$groupName]["VALUE"][$id] = &$directoryValues[$id];
    $arResult["ITEMS"][$groupName]["LINK"][$id] = \Rodzeta\Referenceattribs\Utils::getUrl($currentUrlSegments, $directoryValues[$id]["CODE"]);
	}
}

if (defined("ERROR_404")) {
  // init params for filter
  list($filter, $currentUrl, $currentSectionId, $selectedIds) = \Rodzeta\Referenceattribs\Filter::get($arResult["CURRENT_SECTION_URL"]);
  if ($filter !== false) {
    $arResult["CURRENT_SECTION_URL"] = $currentUrl;
    $arResult["CURRENT_SECTION_ID"] = $currentSectionId;
    $arResult["SELECTED_VALUES"] = $selectedIds;
    $GLOBALS["RODZETA_CATALOG_FILTER"] = $filter;

    //CHTTP::SetStatus("200 OK");

    /*
    if (count($productIds) > 0) {
      $arResult["URL_TEMPLATES"]["section"] = "catalog/";
      $arResult["URL_TEMPLATES"]["element"] = "catalog/#ELEMENT_CODE#/";
      //var_dump($arResult["FOLDER"], $arResult["URL_TEMPLATES"]);

      // FIX set status ok



      $hideSectionList = true;

      // prepare filter by product ids
      global $appCatalogFilter;
      $arParams["FILTER_NAME"] = "appCatalogFilter";
      $arParams["SHOW_ALL_WO_SECTION"] = "Y";
      $arResult["VARIABLES"]["SECTION_ID"] = null;
      $appCatalogFilter = ["ID" => $productIds];

      //include $_SERVER["DOCUMENT_ROOT"] . "/" . $this->GetFolder() . "/section_horizontal.php";

    }
    */
  }

}

// sort by name
//usort($arResult["ITEMS"], function ($a, $b) {
//  return strcmp($a["NAME"], $b["NAME"]);
//});

//$this->SetResultCacheKeys(array(
//  "ID",
//));

/*


// init by current category

//	$filter = null;
//	$currentUrl = $APPLICATION->GetCurPage();
//	$currentSectionId = $arResult["VARIABLES"]["SECTION_ID"];
//  $selectedIds = [];


$filterParams = $storage->get("filter/" . $currentSectionId . "_params");

//Log::info($filterParams, "filter params for category " . $currentSectionId);

//echo "<pre>"; var_dump($filter, $currentUrl, $currentSectionId); print_r($filterParams); echo "</pre>";

$directoryValues = $storage->get("catalog/dir_values");
$directoryFields = [];
foreach ($configEntityAttribs as $field)  {
  if (!empty($field["DIRECTORY"])) {
    $directoryFields[$field["DIRECTORY"]] = $field["CODE"];
  }
}
if (!empty($arParams["FILTER_NAME"])) {
  // filter params by request params

  $tmpFilter = $GLOBALS[$arParams["FILTER_NAME"]];
  $tmpFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

  //Log::info($tmpFilter, "select all values");

  // TODO use filter index
  // get all existing params in results
  $realFilterParams = [];
  foreach (Entity::select($tmpFilter) as $item) {
    // init range params
    foreach ($filterParams as $requestParamName => $v) {
      $code = $v["CODE"];
      if (empty($item["CUSTOMFIELDS"][$code])) {
        continue;
      }
      $value = $item["CUSTOMFIELDS"][$code];
      if (isset($filterParams[$requestParamName]["MIN"]) || isset($filterParams[$requestParamName]["MAX"])) {
        // min
        if (!isset($realFilterParams[$requestParamName]["MIN"])) {
          $realFilterParams[$requestParamName]["MIN"] = $value;
        }
        if ($value < $realFilterParams[$requestParamName]["MIN"]) {
          $realFilterParams[$requestParamName]["MIN"] = $value;
        }

        // max
        if (!isset($realFilterParams[$requestParamName]["MAX"])) {
          $realFilterParams[$requestParamName]["MAX"] = $value;
        }
        if ($value > $realFilterParams[$requestParamName]["MAX"]) {
          $realFilterParams[$requestParamName]["MAX"] = $value;
        }
      }
    }
    // init directory params
    foreach (Entity::getElementSections($item["ID"]) as $tmp) {
      $value = $tmp["ID"];
      if (isset($directoryValues[$value]) &&
          isset($directoryFields[$directoryValues[$value]["GROUP_ID"]])) {
        $code = $directoryFields[$directoryValues[$value]["GROUP_ID"]];
        if (empty($realFilterParams[$code][$value])) {
          $realFilterParams[$code][$value] = 1;
        }
      }
    }
  }

  //Log::info($realFilterParams, "real filter params");
  //print_r($filterParams);

  // update $filterParams by $realFilterParams
  foreach ($filterParams as $requestParamName => $v) {
    $field = &$configEntityAttribs[$v["CODE"]];
    if (!isset($realFilterParams[$requestParamName])) {
      unset($filterParams[$requestParamName]);
    } else if (!empty($field["DIRECTORY"])) {
      foreach ($v["VALUES"] as $id => $idx) {
        if (!isset($realFilterParams[$v["CODE"]][$id])) {
          unset($filterParams[$requestParamName]["VALUES"][$id]);
        }
      }
    } else if (isset($filterParams[$requestParamName]["MIN"]) || isset($filterParams[$requestParamName]["MAX"])) {
      $filterParams[$requestParamName]["MIN"] = $realFilterParams[$requestParamName]["MIN"];
      $filterParams[$requestParamName]["MAX"] = $realFilterParams[$requestParamName]["MAX"];
    }
  }

  //print_r($filterParams);
}

$APPLICATION->SetAdditionalCSS("/bower_components/ion.rangeSlider/css/ion.rangeSlider.css");
$APPLICATION->SetAdditionalCSS("/bower_components/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css");

*/

$this->IncludeComponentTemplate();
