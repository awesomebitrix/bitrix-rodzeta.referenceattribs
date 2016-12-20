<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs;

const ID = "rodzeta.referenceattribs";
const APP = __DIR__ . "/";
const LIB = APP  . "lib/";
const URL_ADMIN = "/bitrix/admin/" . ID . "/";

define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/upload/"
	. (substr($_SERVER["SERVER_NAME"], 0, 4) == "www."?
			substr($_SERVER["SERVER_NAME"], 4) : $_SERVER["SERVER_NAME"])
	. "/." . ID . "/");

require LIB . "encoding/php-array.php";
require LIB . "options.php";
require LIB . "filter.php";

function StorageInit() {
	$path = CONFIG;
	if (!is_dir($path)) {
		mkdir($path, 0700, true);
	}
}

function FromImport($attribs) {
	$result = [];
	foreach (explode("\n\n", $attribs) as $group) {
		$referenceGroup = array_filter(array_map("trim", explode("\n", trim($group))));
		if (count($referenceGroup) == 0) {
			continue;
		}
		list($referenceName, $referenceCode, $referenceSort) = array_map("trim", explode(";", array_shift($referenceGroup)));
		$referenceValues = [];
		foreach ($referenceGroup as $v) {
			list($valueName, $valueSef, $valueSort) = array_map("trim", explode(";", $v));
			$referenceValues[] = [
				"NAME" => $valueName,
        "ALIAS" => $valueSef,
        "SORT" => $valueSort,
			];
		}
		$result[$referenceCode] = [
			"CODE" => $referenceCode,
			"NAME" => $referenceName,
			"SORT" => $referenceSort,
			"VALUES" => $referenceValues,
		];
	}
	return $result;
}

function FromSections() {
	$references = [];
	$currentOptions = Options\Select();
	$iblockId = $currentOptions["iblock_content"];
	$mainSectionId = $currentOptions["section_references"];

	// collect references types
	$res = \CIBlockSection::GetList(
		["SORT" => "ASC"],
		[
			"IBLOCK_ID" => $iblockId,
			"SECTION_ID" => $mainSectionId,
		],
		true,
		["*"]
	);
	// collect references
 	while ($rowReference = $res->GetNext()) {
 		$references[] = implode(";", [$rowReference["NAME"], $rowReference["CODE"], $rowReference["SORT"]]);
 		// collect values
 		$resValue = \CIBlockSection::GetList(
			["SORT" => "ASC"],
			[
				"IBLOCK_ID" => $iblockId,
				"SECTION_ID" => $rowReference["ID"],
			],
			true,
			["*"]
		);
		while ($rowValue = $resValue->GetNext()) {
			$references[] = implode(";", [$rowValue["NAME"], $rowValue["CODE"], $rowValue["SORT"]]);
		}
		$references[] = "";
	}

 	return implode("\n", $references);
}

function Update($attribs) {
	$sort = function ($a, $b) {
		return (int)$a["SORT"] <=> (int)$b["SORT"];
	};

	$currentOptions = Options\Select();
	$iblockId = $currentOptions["iblock_content"];
	$mainSectionId = $currentOptions["section_references"];

	$sefCodes = [];
	$result = [];
	$errors = [];
	$values = [];
	foreach ($attribs as $row) {
		$row["CODE"] = trim($row["CODE"]);
		if ($row["CODE"] == "" || count(array_filter($row)) == 0) {
			continue;
		}
		$row["SORT"] = (int)$row["SORT"];

		// create or update section for attrib
		if ($mainSectionId) {
			$res = \CIBlockSection::GetList(
				["SORT" => "ASC"],
				[
					"IBLOCK_ID" => $iblockId,
					"SECTION_ID" => $mainSectionId,
					"CODE" => $row["CODE"],
				],
				true,
				["*"]
			);
			$referenceSection = $res->GetNext();
			$iblockSection = new \CIBlockSection();
			if (empty($referenceSection["ID"])) {
				$data = [
				  "IBLOCK_ID" => $iblockId,
				  "IBLOCK_SECTION_ID" => $mainSectionId,
				  "NAME" => $row["NAME"],
				  "CODE" => $row["CODE"],
					"ACTIVE" => "Y",
			  ];
			  if (!empty($row["SORT"])) {
			  	$data["SORT"] = $row["SORT"];
			  }
				$row["SECTION_ID"] = $iblockSection->Add($data);
			} else {
				$data = [
				  "IBLOCK_ID" => $iblockId,
				  "NAME" => $row["NAME"],
				  "CODE" => $row["CODE"],
					"ACTIVE" => "Y",
			  ];
			  if (!empty($row["SORT"])) {
			  	$data["SORT"] = $row["SORT"];
			  }
				$row["SECTION_ID"] = $referenceSection["ID"];
				$iblockSection->Update($row["SECTION_ID"], $data);
			}
		}

		// collect sef codes
		foreach ($row["VALUES"] as $i => $v) {
			$v["NAME"] = trim($v["NAME"]);
			$v["ALIAS"] = trim($v["ALIAS"]);
			// reset empty values
			if ($v["NAME"] == "" || $v["ALIAS"] == "") {
				unset($row["VALUES"][$i]);
				continue;
			}
			// collect duplicate sef aliases
			if (isset($sefCodes[$v["ALIAS"]])) {
				$errors["BY_ALIAS"][] = $row["CODE"] . ": " . $v["ALIAS"];
			}

			$sefCodes[$v["ALIAS"]] = [$row["CODE"], $i];
			if ($mainSectionId) {
				$sectionValue = [];
				// create or update section for value
				$filterValue = [
					"IBLOCK_ID" => $iblockId,
					"SECTION_ID" => $row["SECTION_ID"],
				];
				// get value data by code
				if (!empty($v["ID"])) {
					$filterValue["ID"] = $v["ID"];
					$res = \CIBlockSection::GetList(
						["SORT" => "ASC"],
						$filterValue,
						true,
						["*"]
					);
					$sectionValue = $res->GetNext();
				}
				// find by ALIAS (CODE)
				if (empty($sectionValue["ID"])) {
					unset($filterValue["ID"]);
					$filterValue["CODE"] = $v["ALIAS"];
					$res = \CIBlockSection::GetList(
						["SORT" => "ASC"],
						$filterValue,
						true,
						["*"]
					);
					$sectionValue = $res->GetNext();
				}
				$iblockSection = new \CIBlockSection();
				if (empty($sectionValue["ID"])) {
					$data = [
					  "IBLOCK_ID" => $iblockId,
					  "IBLOCK_SECTION_ID" => $row["SECTION_ID"],
					  "NAME" => $v["NAME"],
					  "CODE" => $v["ALIAS"],
						"ACTIVE" => "Y",
				  ];
				  if (!empty($v["SORT"])) {
				  	$data["SORT"] = $v["SORT"];
				  }
					$v["ID"] = $iblockSection->Add($data);
				} else {
					$v["ID"] = $sectionValue["ID"];
					$data = [
					  "IBLOCK_ID" => $iblockId,
					  "NAME" => $v["NAME"],
					  "CODE" => $v["ALIAS"],
						"ACTIVE" => "Y",
				  ];
					if (!empty($v["SORT"])) {
				  	$data["SORT"] = $v["SORT"];
				  }
					$iblockSection->Update($v["ID"], $data);
				}

				// collect value ids
				$values[$v["ID"]] = [$row["CODE"], $i];
			}
			$row["VALUES"][$i] = $v;
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

	// get all urls for catalog sections
	$res = \CIBlockSection::GetByID($currentOptions["section_content"]);
	$sectionCatalog = $res->GetNext();
	$res = \CIBlockSection::GetList(
		["SORT" => "ASC"],
		[
			"IBLOCK_ID" => $iblockId,
			"ACTIVE" => "Y",
		],
		true,
		["*"]
	);
	$catalogSections = [];
	$l = strlen($sectionCatalog["SECTION_PAGE_URL"]);
	while ($row = $res->GetNext()) {
		if (substr($row["SECTION_PAGE_URL"], 0, $l) === $sectionCatalog["SECTION_PAGE_URL"]) {
			$catalogSections[$row["SECTION_PAGE_URL"]] = $row["ID"];
		}
	}

	\Encoding\PhpArray\Write(CONFIG . "references.php", [
		$result, $sefCodes, $catalogSections, $values
	]);

	return $errors;
}

function Select() {
	$fname = CONFIG . "references.php";
	return is_readable($fname)? include $fname
		: [	[], [], [], [] ];
}

// TODO remove
function Config() {
	return include $_SERVER["DOCUMENT_ROOT"] . FILE_ATTRIBS;
}

function Url($segments, $param) {
	$tmp = array_flip($segments);
	if (!isset($segments[$param])) {
		$tmp[] = $param;
	}
	return "/" . implode("/", $tmp) . "/";
}

function Init(&$item) {
	static $config = null;
	if (empty($config)) {
		$config = Config();
	}
	$res = \CIBlockElement::GetElementGroups($item["ID"], true, ["ID"]);
	while ($section = $res->Fetch()) {
		if (isset($config[3][$section["ID"]])) {
			list($code, $valueIdx) = $config[3][$section["ID"]];
			// filter by attrib sections
			if (!isset($config[0][$code]["SECTIONS"][$item["IBLOCK_SECTION_ID"]])) {
				continue;
			}
			if (!isset($item["PROPERTIES"][$code])) {
				$item["PROPERTIES"][$code] = [
					"CODE" => &$config[0][$code]["CODE"],
					"NAME" => &$config[0][$code]["NAME"],
					"VALUE" => [&$config[0][$code]["VALUES"][$valueIdx]["NAME"]],
					"~VALUE" => [&$config[0][$code]["VALUES"][$valueIdx]],
				];
			} else {
				$item["PROPERTIES"][$code]["VALUE"][] =
					&$config[0][$code]["VALUES"][$valueIdx]["NAME"];
				$item["PROPERTIES"][$code]["~VALUE"][] =
					&$config[0][$code]["VALUES"][$valueIdx];
			}
		}
  }
}

function AppendValues($data, $n, $v) {
	yield from $data;
	for ($i = 0; $i < $n; $i++) {
		yield  $v;
	}
}

function SectionsTreeList($currentIblockId) {
	$resSections = \CIBlockSection::GetTreeList(
		["IBLOCK_ID" => $currentIblockId],
		["ID", "NAME", "DEPTH_LEVEL"]
	);
	$sections = [];
	while ($section = $resSections->GetNext()) {
	  $sections[$section["ID"]] = str_repeat(" . ", $section["DEPTH_LEVEL"] - 1)
	  	. $section["NAME"];
	}
	return $sections;
}
