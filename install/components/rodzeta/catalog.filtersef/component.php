<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Config\Option;

//$path = $this->GetPath();

// $arParams

$arResult["ITEMS"] = array();

list($attribs, $sefCodes, $catalogSections, $values) = Config();

$arResult["CURRENT_SECTION_URL"] = $APPLICATION->GetCurPage(false);
$arResult["CURRENT_SECTION_ID"] = null;
$arResult["SELECTED_VALUES"] = array();

// RODZETA_CATALOG_FILTER need for other components parametr $arParams["FILTER_NAME"]
$GLOBALS["RODZETA_CATALOG_FILTER"] = array();

$currentUrlSegments = array_flip(array_filter(
  explode("/", $arResult["CURRENT_SECTION_URL"])));

// init link for each value
foreach ($attribs as $code => $row) {
  foreach ($row["VALUES"] as $i => $v) {
    $attribs[$code]["VALUES"][$i]["LINK"] =
      Url($currentUrlSegments, $v["ALIAS"]);
  }
}
$arResult["ITEMS"] = $attribs;

if (defined("ERROR_404")) { // section with filter
  // init params for filter
  list($filter, $currentUrl, $currentSectionId, $selectedIds) =
    Filter($arResult["CURRENT_SECTION_URL"]);
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
  if (isset($catalogSections[$arResult["CURRENT_SECTION_URL"]])) {
    $arResult["CURRENT_SECTION_ID"] = $catalogSections[$arResult["CURRENT_SECTION_URL"]];
  }
}

// filter params for current section
if (!empty($arResult["CURRENT_SECTION_ID"])) {
  foreach ($arResult["ITEMS"] as $code => $row) {
    if (!isset($row["SECTIONS"][$arResult["CURRENT_SECTION_ID"]])) {
      unset($arResult["ITEMS"][$code]);
    }
  }
}

//$this->SetResultCacheKeys(array(
//  "ID",
//));

$this->IncludeComponentTemplate();
