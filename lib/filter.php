<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Referenceattribs;

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;

final class Filter {

	static function getIds($segments) {
		$result = array();

		$storage = new Storage(_APP_ROOT . "/api");
		$allSectionUrls = $storage->get("category/routes");
		$filterParams = [];
		while (count($segments) > 0) {
			$url = "/" . implode("/", $segments) . "/";
			if (isset($allSectionUrls[$url])) {
				break;
			}
			array_unshift($filterParams, array_pop($segments));
		}
		$currentSectionId = $allSectionUrls[$url];
		unset($allSectionUrls);

		// create filter by aliases
		$groups = [];
		$byCode = $storage->get("catalog/dir_value_alias");
		$byId = $storage->get("catalog/dir_values");

		$selectedIds = [];
		foreach ($filterParams as $alias) {
			if (!isset($byCode[$alias])) {
				// nonvalid param in url
				return [false, $url, $currentSectionId];
			}
			$param = $byId[$byCode[$alias]];
			$groups[$param["GROUP_ID"]][] = $param["ID"];
			$selectedIds[$param["ID"]] = 1;
		}
		$result = [];
		foreach ($groups as $ids) {
			$result[] = ["SECTION_ID" => $ids];
		}

		$iblockId = Option::get("rodzeta.referenceattribs", "iblock_id", 2);
		$result = [
			"IBLOCK_ID" => $iblockId,
			"INCLUDE_SUBSECTIONS" => "Y",
			"LOGIC" => "AND",
			$result
		];
		if ($currentSectionId != self::CATALOG_SECTION_ID) {
			$result["ID"] = CIBlockElement::SubQuery("ID", [
        "IBLOCK_ID" => $iblockId,
        "SECTION_ID" => $currentSectionId,
        "INCLUDE_SUBSECTIONS" => "Y",
      ]);
		}
		return [$result, $url, $currentSectionId, $selectedIds];
	}

}