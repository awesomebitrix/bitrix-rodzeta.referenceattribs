<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?= BeginNote() ?>
<p>
	<b>Как работает</b>
	<ul>
		<li>структура раздела "Справочники": раздел - тип значения (например Цвет), его подразделы - значения (например Красный, Желтый, Зеленый);
		<li>после изменений в разделе "Справочники" - нажмите в настройке модуля кнопку "Применить настройки";
	</ul>
</p>
<?= EndNote() ?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.referenceattribs", "iblock_id", (int)$request->getPost("iblock_id"));
		Option::set("rodzeta.referenceattribs", "section_code", $request->getPost("section_code"));
		Option::set("rodzeta.referenceattribs", "catalog_section_id", (int)$request->getPost("catalog_section_id"));

		\Rodzeta\Referenceattribs\Utils::createCache();

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}	/*else if ($request->getPost("clear") != "") {


		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_OPTIONS_RESETED"),
	    "TYPE" => "OK",
	  ));
	} */
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<?php /*
	<tr class="heading">
		<td colspan="2">Настройки для свойств-справочников</td>
	</tr>
	*/ ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок содержащий "Справочники"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				Option::get("rodzeta.referenceattribs", "iblock_id", 2),
				"iblock_type_id",
				"iblock_id",
				array(
					"MIN_PERMISSION" => "R",
				),
				"",
				""
			) ?>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>ID раздела "Справочники"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="section_code" type="text" value="RODZETA_REFERENCE" readonly>
			<?php /* <?= Option::get("rodzeta.referenceattribs", "section_code") ?> */ ?>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>ID раздела "Каталог"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="catalog_section_id" type="text" size="4" value="<?= Option::get("rodzeta.referenceattribs", "catalog_section_id", 7) ?>">
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<?php

$tabControl->end();
