<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class rodzeta_referenceattribs extends CModule {

	var $MODULE_ID = "rodzeta.referenceattribs"; // FIX for bitrix rules

	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_GROUP_RIGHTS;
	public $PARTNER_NAME;
	public $PARTNER_URI;

	//public $MODULE_GROUP_RIGHTS = 'N';
	//public $NEED_MAIN_VERSION = '';
	//public $NEED_MODULES = array();

	function __construct() {
		$this->MODULE_ID = "rodzeta.referenceattribs"; // NEED for showing module in /bitrix/admin/partner_modules.php?lang=ru

		$arModuleVersion = array();
		include __DIR__ . "/version.php";

		if (!empty($arModuleVersion["VERSION"])) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = Loc::getMessage("RODZETA_REFERENCEATTRIBS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("RODZETA_REFERENCEATTRIBS_MODULE_DESCRIPTION");
		$this->MODULE_GROUP_RIGHTS = "N";

		$this->PARTNER_NAME = "Rodzeta";
		$this->PARTNER_URI = "http://rodzeta.ru/";
	}

	function InstallFiles() {
		CopyDirFiles(
	    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin/" . $this->MODULE_ID,
	    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/" . $this->MODULE_ID,
	    true, true
    );
    CopyDirFiles(
	    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components/rodzeta/catalog.filtersef",
	    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/rodzeta/catalog.filtersef",
	    true, true
    );
		return true;
	}

	function UnInstallFiles() {
		DeleteDirFilesEx("/bitrix/admin/" . $this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/components/rodzeta/catalog.filtersef");
		return true;
	}

	function DoInstall() {
		ModuleManager::registerModule($this->MODULE_ID);
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		$this->InstallFiles();
	}

	function DoUninstall() {
		$this->UnInstallFiles();
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		ModuleManager::unregisterModule($this->MODULE_ID);
	}

}
