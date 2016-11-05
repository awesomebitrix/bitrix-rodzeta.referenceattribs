<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Config\Option;

//$path = $this->GetPath();

// $arParams

$arResult["ITEMS"] = array();

// TODO rewrite for new config structure

list($directoryValues, $dirAliases, $directoryGroups, $sectionsPaths) = \Rodzeta\Referenceattribs\Config();

//$arResult["USE_OPTIONS_LINKS"] = Option::get("rodzeta.referenceattribs", "use_options_links");
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
    $arResult["ITEMS"][$groupName]["LINK"][$id] = \Rodzeta\Referenceattribs\Url($currentUrlSegments, $directoryValues[$id]["CODE"]);
	}
}

if (defined("ERROR_404")) { // section with filter
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

} else { // section url
  if (isset($sectionsPaths[$arResult["CURRENT_SECTION_URL"]])) {
    $arResult["CURRENT_SECTION_ID"] = $sectionsPaths[$arResult["CURRENT_SECTION_URL"]];
  }
}

// filter params for current section
if (!empty($arResult["CURRENT_SECTION_ID"])) {
  foreach ($arResult["ITEMS"] as $groupName => $v) {
    if (empty($v["GROUP"]["UF_SECTIONS"]) || !in_array($arResult["CURRENT_SECTION_ID"], $v["GROUP"]["UF_SECTIONS"])) {
      unset($arResult["ITEMS"][$groupName]);
    }
  }
}

//$this->SetResultCacheKeys(array(
//  "ID",
//));

$this->IncludeComponentTemplate();
