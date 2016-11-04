<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Referenceattribs;

use Bitrix\Main\Config\Option;

define(__NAMESPACE__ . "\_APP", __DIR__ . "/");
define(__NAMESPACE__ . "\_LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\_FILE_ATTRIBS", "/upload/.rodzeta.referenceattribs.php");

require _LIB . "encoding/php-array.php";

/*
function CreateCache() {
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
		$basePath . _FILE_ATTRIBS,
		"<?php\nreturn " . var_export(array($attribs, $sefCodes, $groups, $catalogSections), true) . ";"
	);
}
*/

function CreateCache($attribs) {
	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$iblockId = Option::get("rodzeta.referenceattribs", "iblock_id", 2);

	$sort = function ($a, $b) {
		if ($a["SORT"] == $b["SORT"]) {
			return 0;
		}
		return ((int)$a["SORT"] < (int)$b["SORT"]) ? -1 : 1;
	};

	// create section RODZETA_REFERENCES
	$res = \CIBlockSection::GetList(
		array("SORT" => "ASC"),
		array(
			"IBLOCK_ID" => $iblockId,
			"CODE" => "RODZETA_REFERENCES",
		),
		true,
		array("*")
	);
	$sectionReferences = $res->GetNext();
	if (empty($sectionReferences["ID"])) {
		$iblockSection = new \CIBlockSection();
		$mainSectionId = $iblockSection->Add(array(
		  "IBLOCK_ID" => $iblockId,
		  "NAME" => "Справочники",
		  "CODE" => "RODZETA_REFERENCES",
		  "SORT" => 10000,
			"ACTIVE" => "Y",
	  ));
	  if (!empty($mainSectionId)) {
	  	Option::set("rodzeta.referenceattribs", "section_id", $mainSectionId);
	  }
	} else {
		$mainSectionId = $sectionReferences["ID"];
	}

	$sefCodes = array();
	$result = array();
	$errors = array();
	foreach ($attribs as $row) {
		$row["CODE"] = trim($row["CODE"]);
		if ($row["CODE"] == "" || count(array_filter($row)) == 0) {
			continue;
		}
		$row["SORT"] = (int)$row["SORT"];

		if ($mainSectionId) {
			// create or update section for attrib
			$res = \CIBlockSection::GetList(
				array("SORT" => "ASC"),
				array(
					"IBLOCK_ID" => $iblockId,
					"SECTION_ID" => $mainSectionId,
					"CODE" => $row["CODE"],
				),
				true,
				array("*")
			);
			$sectionGroup = $res->GetNext();
			$iblockSection = new \CIBlockSection();
			if (empty($sectionGroup["ID"])) {
				$row["SECTION_ID"] = $iblockSection->Add(array(
				  "IBLOCK_ID" => $iblockId,
				  "IBLOCK_SECTION_ID" => $mainSectionId,
				  "NAME" => $row["NAME"],
				  "CODE" => $row["CODE"],
				  "SORT" => $row["SORT"],
					"ACTIVE" => "Y",
			  ));
			} else {
				$row["SECTION_ID"] = $sectionGroup["ID"];
				$iblockSection->Update($row["SECTION_ID"], array(
				  "IBLOCK_ID" => $iblockId,
				  "NAME" => $row["NAME"],
				  "CODE" => $row["CODE"],
				  "SORT" => $row["SORT"],
					"ACTIVE" => "Y",
			  ));
			}
		}

		// collect sef codes
		foreach ($row["VALUES"] as $i => $v) {
			$v["NAME"] = trim($v["NAME"]);
			$v["ALIAS"] = trim($v["ALIAS"]);
			if ($v["NAME"] != "" && $v["ALIAS"] != "") {
				if (!isset($sefCodes[$v["ALIAS"]])) {
					$sefCodes[$v["ALIAS"]] = array($row["CODE"], $i);

					if ($mainSectionId) {
						$sectionValue = array();
						// create or update section for value
						$filterValue = array(
							"IBLOCK_ID" => $iblockId,
							"SECTION_ID" => $row["SECTION_ID"],
						);
						if (!empty($v["ID"])) {
							$filterValue["ID"] = $v["ID"];
							$res = \CIBlockSection::GetList(
								array("SORT" => "ASC"),
								$filterValue,
								true,
								array("*")
							);
							$sectionValue = $res->GetNext();
						}
						if (empty($sectionValue["ID"])) {
							// find by CODE = ALIAS
							unset($filterValue["ID"]);
							$filterValue["CODE"] = $v["ALIAS"];
							$res = \CIBlockSection::GetList(
								array("SORT" => "ASC"),
								$filterValue,
								true,
								array("*")
							);
							$sectionValue = $res->GetNext();
						}
						$iblockSection = new \CIBlockSection();
						if (empty($v["SORT"])) {
							$v["SORT"] = ($i + 1) * 100;
						}
						if (empty($sectionValue["ID"])) {
							$v["ID"] = $iblockSection->Add(array(
							  "IBLOCK_ID" => $iblockId,
							  "IBLOCK_SECTION_ID" => $row["SECTION_ID"],
							  "NAME" => $v["NAME"],
							  "CODE" => $v["ALIAS"],
							  "SORT" => $v["SORT"],
								"ACTIVE" => "Y",
						  ));
						} else {
							$v["ID"] = $sectionValue["ID"];
							$iblockSection->Update($v["ID"], array(
							  "IBLOCK_ID" => $iblockId,
							  "NAME" => $v["NAME"],
							  "CODE" => $v["ALIAS"],
							  "SORT" => $v["SORT"],
								"ACTIVE" => "Y",
						  ));
						}
					}
				} else {
					$errors["BY_ALIAS"][] = $row["CODE"] . ": " . $v["ALIAS"];
				}
				$row["VALUES"][$i] = $v;
			} else {
				unset($row["VALUES"][$i]);
			}
		}
		// convert sections ids
		if (!empty($row["SECTIONS"])) {
			$row["SECTIONS"] = array_flip(explode(",", $row["SECTIONS"]));
		}
		// ordering attribs by key SORT
		usort($row["VALUES"], $sort);

		$result[$row["CODE"]] = $row;
	}

	// ordering attribs by key SORT
	uasort($result, $sort);

	// TODO all real sections list

	\Encoding\PhpArray\Write($basePath . _FILE_ATTRIBS, array($result, $sefCodes));

	return $errors;
}

function Config() {
	return include $_SERVER["DOCUMENT_ROOT"] . _FILE_ATTRIBS;
}

/*
function Init(&$item) {
	if (empty($item["PROPERTIES"]["RODZETA_ATTRIBS"])) {
		return;
	}
	if (!empty($item["DISPLAY_PROPERTIES"]["RODZETA_ATTRIBS"])) {
		unset($item["DISPLAY_PROPERTIES"]["RODZETA_ATTRIBS"]);
	}
	$attribs = &$item["PROPERTIES"]["RODZETA_ATTRIBS"];
	$tmp = array();
	foreach ($attribs["~VALUE"] as $i => $v) {
		if (!empty($attribs["DESCRIPTION"][$i])) {
			$tmp[$attribs["DESCRIPTION"][$i]] = $v;
		}
	}
	// sort
	static $config = null;
	if (empty($config)) {
		list($config) = Config();
	}
	foreach ($config as $code => $v) {
		if (isset($tmp[$code])) {
			$item["PROPERTIES"][$code] = array(
				"CODE" => &$config[$code]["CODE"],
				"NAME" => &$config[$code]["NAME"],
				"HINT" => &$config[$code]["HINT"],
				"VALUE" => $tmp[$code],
			);
			$item["PROPERTIES"][$code]["~VALUE"] = &$item["PROPERTIES"][$code]["VALUE"];
		}
	}
	unset($item["PROPERTIES"]["RODZETA_ATTRIBS"]);
	//unset($item["PROPERTIES"]["LINKS"]);
}

function BuildTree(&$elements, $parentId = 0) {
	$branch = array();
	foreach ($elements as &$element) {
		if ($element["PARENT_ID"] == $parentId) {
			$children = BuildTree($elements, $element["ID"]);
			if ($children) {
				$element["CHILDREN"] = $children;
			}
			$branch[$element["ID"]] = $element;
			unset($element);
		}
	}
	return $branch;
}

function PrintTree($elements, &$result, $level = 0) {
	foreach ($elements as $element) {
		$result[$element["ID"]] = str_repeat(" -", $level) . " " . $element["NAME"];
		PrintTree($element["CHILDREN"], $result, $level + 1);
	}
}
*/

function AppendValues($data, $n, $v) {
	for ($i = 0; $i < $n; $i++) {
		$data[] = $v;
	}
	return $data;
}

function SectionsTreeList($currentIblockId) {
	$resSections = \CIBlockSection::GetTreeList(
		array("IBLOCK_ID" => $currentIblockId),
		array("ID", "NAME", "DEPTH_LEVEL")
	);
	$sections = array();
	while ($section = $resSections->GetNext()) {
	  $sections[$section["ID"]] = str_repeat(" . ", $section["DEPTH_LEVEL"] - 1) . $section["NAME"];
	}
	return $sections;
}
