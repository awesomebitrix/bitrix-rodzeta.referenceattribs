<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//$path = $this->GetPath();

// $arParams

$arResult["ITEMS"] = array();

list($directoryValues, $dirAliases, $directoryGroups, $sectionsPaths) = \Rodzeta\Referenceattribs\Utils::get();

foreach ($directoryGroups as $group => $values) {
	$groupName = $directoryValues[$group]["NAME"];
	$arResult["ITEMS"][$groupName] = array(
		"GROUP" => &$directoryValues[$group],
		"VALUE" => null,
	);
	foreach ($values as $id) {
		$arResult["ITEMS"][$groupName]["VALUE"][$id] = &$directoryValues[$id];
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
global $_APP;

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

$currentUrlSegments = array_flip(array_filter(explode("/", $APPLICATION->GetCurPage())));

$makeUrlWithParam = function ($segments, $param) {
	$tmp = array_flip($segments);
	if (!isset($segments[$param])) {
		$tmp[] = $param;
	}
	return "/" . implode("/", $tmp) . "/";
};
*/

$this->IncludeComponentTemplate();
