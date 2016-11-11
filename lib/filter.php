<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs;

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;

function Filter($segments, $config = null) {
	if (is_string($segments)) {
		$segments = explode("/", trim($segments, "/"));
	}
	list($attribs, $sefCodes, $catalogSections, $values) = Config();

	$filterParams = array();
	// detect current section url
	$currentUrl = "/";
	while (count($segments) > 0) {
		$url = "/" . implode("/", $segments) . "/";
		if (isset($catalogSections[$url])) {
			$currentUrl = $url;
			break;
		}
		// store rest segments as params
		array_unshift($filterParams, array_pop($segments));
	}
	$currentSectionId = $catalogSections[$url];

	// create bitrix filter params
	$selectedIds = array();
	$selectedGroups = array();
	foreach ($filterParams as $alias) {
		if (!isset($sefCodes[$alias])) {
			// nonvalid param in url
			return array(false, $currentUrl, $currentSectionId, array());
		}
		list($code, $valueIdx) = $sefCodes[$alias];
		$param = $attribs[$code]["VALUES"][$valueIdx];
		$selectedGroups[$attribs[$code]["SECTION_ID"]][] = $param["ID"];
		$selectedIds[$param["ID"]] = 1;
	}
	$result = array();
	foreach ($selectedGroups as $ids) {
		$result[] = array("SECTION_ID" => $ids);
	}

	$iblockId = Option::get("rodzeta.site", "iblock_content", 1);
	if (count($selectedGroups)) {
		$result = array(
			"IBLOCK_ID" => $iblockId,
			"INCLUDE_SUBSECTIONS" => "Y",
			"LOGIC" => "AND",
			$result
		);
	} else {
		$result = array(
			"IBLOCK_ID" => $iblockId,
			"INCLUDE_SUBSECTIONS" => "Y",
		);
	}
	if ($currentSectionId != Option::get("rodzeta.referenceattribs", "catalog_section_id", 2)) {
		$result["ID"] = \CIBlockElement::SubQuery("ID", array(
      "IBLOCK_ID" => $iblockId,
      "SECTION_ID" => $currentSectionId,
      "INCLUDE_SUBSECTIONS" => "Y",
    ));
	}
	return array($result, $currentUrl, $currentSectionId, $selectedIds);
}
