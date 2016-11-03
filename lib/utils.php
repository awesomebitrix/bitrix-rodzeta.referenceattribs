<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Referenceattribs;

use Bitrix\Main\Config\Option;

final class Utils {

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

	static function getUrl($segments, $param) {
		$tmp = array_flip($segments);
		if (!isset($segments[$param])) {
			$tmp[] = $param;
		}
		return "/" . implode("/", $tmp) . "/";
	}

}
