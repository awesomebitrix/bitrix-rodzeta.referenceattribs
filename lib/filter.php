<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs;

use Bitrix\Main\Application;

function Filter($segments, $config = null) {
	if (is_string($segments)) {
		$segments = explode("/", trim($segments, "/"));
	}
	list($attribs, $sefCodes, $catalogSections, $values) = Config();

	$filterParams = [];
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
	$selectedIds = [];
	$selectedGroups = [];
	foreach ($filterParams as $alias) {
		if (!isset($sefCodes[$alias])) {
			// nonvalid param in url
			return [false, $currentUrl, $currentSectionId, []];
		}
		list($code, $valueIdx) = $sefCodes[$alias];
		$param = $attribs[$code]["VALUES"][$valueIdx];
		$selectedGroups[$attribs[$code]["SECTION_ID"]][] = $param["ID"];
		$selectedIds[$param["ID"]] = 1;
	}
	$result = [];
	foreach ($selectedGroups as $ids) {
		$result[] = ["SECTION_ID" => $ids];
	}

	$currentOptions = Options\Select();
	$iblockId = $currentOptions["iblock_content"];
	if (count($selectedGroups)) {
		$result = [
			"IBLOCK_ID" => $iblockId,
			"INCLUDE_SUBSECTIONS" => "Y",
			"LOGIC" => "AND",
			$result
		];
	} else {
		$result = [
			"IBLOCK_ID" => $iblockId,
			"INCLUDE_SUBSECTIONS" => "Y",
		];
	}
	if ($currentSectionId != $currentOptions["section_content"]) {
		$result["ID"] = \CIBlockElement::SubQuery("ID", [
      "IBLOCK_ID" => $iblockId,
      "SECTION_ID" => $currentSectionId,
      "INCLUDE_SUBSECTIONS" => "Y",
    ]);
	}
	return [$result, $currentUrl, $currentSectionId, $selectedIds];
}
