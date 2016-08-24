<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Referenceattribs;

use Bitrix\Main\Config\Option;

final class Utils {

	const MAP_NAME = "/upload/cache.rodzeta.referenceattribs.php";

	static function init(&$item) {
		static $config = null;
		if (empty($config)) {
			$config = self::get();
		}
		$res = \CIBlockElement::GetElementGroups($item["ID"], true, array("ID"));
		while ($section = $res->Fetch()) {
			if (isset($config[0][$section["ID"]])) {
				$groupId = $config[0][$section["ID"]]["GROUP_ID"];
				$groupName = $config[0][$groupId]["NAME"];
				$item["REFERENCEATTRIBS"][$groupName]["GROUP"] = &$config[0][$groupId];
				$item["REFERENCEATTRIBS"][$groupName]["VALUE"][$section["ID"]] = &$config[0][$section["ID"]];
			}
	  }
	}

	static function createCache() {
		$basePath = $_SERVER["DOCUMENT_ROOT"];
		$sefCodes = array();
		$attribs = array();
		$groups = array();
		$iblockId = Option::get("rodzeta.referenceattribs", "iblock_id", 2);

		// collect references types
		$sectionId = Option::get("rodzeta.referenceattribs", "section_id");
		if ($sectionId != "") {
			$res = \CIBlockSection::GetList(
				array("SORT" => "ASC"),
				array(
					"IBLOCK_ID" => $iblockId,
					"SECTION_ID" => $sectionId,
					"ACTIVE" => "Y",
				),
				true,
				array("UF_*")
			);
			while ($row = $res->GetNext()) {
				$attribs[$row["ID"]] = array(
					"ID" => $row["ID"],
					"NAME" => $row["NAME"],
					"CODE" => $row["CODE"],
					"DESCRIPTION" => $row["DESCRIPTION"],
					"DETAIL_PICTURE" => $row["DETAIL_PICTURE"],
					"PICTURE" => $row["PICTURE"],
				);
				$sefCodes[$row["CODE"]] = $row["ID"];

				// add UF_ fields
				foreach ($row as $k => $v) {
					if (substr($k, 0, 3) == "UF_") {
						$attribs[$row["ID"]][$k] = $row["~" . $k];
					}
				}
			}
		}

		// collect references values
		foreach ($attribs as $groupId => $v) {
			$res = \CIBlockSection::GetList(
				array("SORT" => "ASC"),
				array(
					"IBLOCK_ID" => $iblockId,
					"SECTION_ID" => $groupId,
					"ACTIVE" => "Y",
				),
				true,
				array("UF_*")
			);
			while ($row = $res->GetNext()) {
				$attribs[$row["ID"]] = array(
					"ID" => $row["ID"],
					"NAME" => $row["NAME"],
					"CODE" => $row["CODE"],
					"DESCRIPTION" => $row["DESCRIPTION"],
					"DETAIL_PICTURE" => $row["DETAIL_PICTURE"],
					"PICTURE" => $row["PICTURE"],
					"GROUP_ID" => $groupId
				);
				$sefCodes[$row["CODE"]] = $row["ID"];

				$groups[$groupId][$row["ID"]] = 1;

				// add UF_ fields
				foreach ($row as $k => $v) {
					if (substr($k, 0, 3) == "UF_") {
						$attribs[$row["ID"]][$k] = $row["~" . $k];
					}
				}
			}
		}

		// get all urls for catalog sections
		$res = \CIBlockSection::GetByID(Option::get("rodzeta.referenceattribs", "catalog_section_id", 7));
		$section = $res->GetNext();
		$res = \CIBlockSection::GetList(
			array("SORT" => "ASC"),
			array(
				"IBLOCK_ID" => $iblockId,
				"ACTIVE" => "Y",
			),
			true,
			array("UF_*")
		);
		$catalogSections = array();
		while ($row = $res->GetNext()) {
			if (substr($row["SECTION_PAGE_URL"], 0,
						strlen($section["SECTION_PAGE_URL"])) === $section["SECTION_PAGE_URL"]) {
				$catalogSections[$row["SECTION_PAGE_URL"]] = $row["ID"];
			}
		}

		file_put_contents(
			$basePath . self::MAP_NAME,
			"<?php\nreturn " . var_export(array($attribs, $sefCodes, $groups, $catalogSections), true) . ";"
		);
	}

	static function getUrl($segments, $param) {
		$tmp = array_flip($segments);
		if (!isset($segments[$param])) {
			$tmp[] = $param;
		}
		return "/" . implode("/", $tmp) . "/";
	}

	static function get() {
		return include $_SERVER["DOCUMENT_ROOT"] . self::MAP_NAME;
	}

}
