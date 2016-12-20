<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs;

defined("B_PROLOG_INCLUDED") and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\{Loader, EventManager};

require __DIR__ . "/.init.php";

Loader::includeModule("iblock");

EventManager::getInstance()->addEventHandler("main", "OnPanelCreate", function () {
	global $USER;
	// TODO заменить на определение доступа к редактированию конента
	if (!$USER->IsAdmin()) {
	  return;
	}

	global $APPLICATION;
	$link = "javascript:" . $APPLICATION->GetPopupLink([
		"URL" => URL_ADMIN,
		"PARAMS" => [
			"resizable" => true,
			//"width" => 780,
			//"height" => 570,
			//"min_width" => 400,
			//"min_height" => 200,
			"buttons" => "[BX.CDialog.prototype.btnClose]"
		]
	]);
  $APPLICATION->AddPanelButton([
		"HREF" => $link,
		"ICON"  => "bx-panel-site-structure-icon",
		//"SRC" => URL_ADMIN . "/icon.gif",
		"TEXT"  => "Настройки свойств-справочников",
		"ALT" => "Настройки свойств-справочников",
		"MAIN_SORT" => 2000,
		"SORT"      => 300
	]);

	$link = "javascript:" . $APPLICATION->GetPopupLink([
		"URL" => URL_ADMIN . "references.php",
		"PARAMS" => [
			"resizable" => true,
			//"width" => 780,
			//"height" => 570,
			//"min_width" => 400,
			//"min_height" => 200,
			"buttons" => "[BX.CDialog.prototype.btnClose]"
		]
	]);
	$APPLICATION->AddPanelButton([
		"HREF" => $link,
		"ICON"  => "bx-panel-site-structure-icon",
		//"SRC" => URL_ADMIN . "/icon.gif",
		"TEXT"  => "Свойства-справочники",
		"ALT" => "Свойства-справочники",
		"MAIN_SORT" => 2000,
		"SORT"      => 310
	]);
});
