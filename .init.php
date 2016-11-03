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
function CreateCache($attribs) {
	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$sefCodes = array();
	$result = array();
	foreach ($attribs as $row) {
		$row["CODE"] = trim($row["CODE"]);
		$row["ALIAS"] = trim($row["ALIAS"]);
		if ($row["CODE"] == "" || count(array_filter($row)) == 0) {
			continue;
		}
		$row["SORT"] = (int)$row["SORT"];
		// collect sef codes
		if (!empty($row["ALIAS"])) {
			$sefCodes[$row["ALIAS"]] = $row["CODE"];
		}
		// convert sections ids
		if (!empty($row["SECTIONS"])) {
			$row["SECTIONS"] = array_flip(explode(",", $row["SECTIONS"]));
		}
		$result[$row["CODE"]] = $row;
	}

	// ordering by key SORT
	uasort($result, function ($a, $b) {
		if ($a["SORT"] == $b["SORT"]) {
			return 0;
		}
		return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
	});

	\Encoding\PhpArray\Write($basePath . _FILE_ATTRIBS, array($result, $sefCodes));
}

function Config() {
	return include $_SERVER["DOCUMENT_ROOT"] . _FILE_ATTRIBS;
}

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
*/
