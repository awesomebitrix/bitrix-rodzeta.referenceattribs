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
		Option::set("rodzeta.referenceattribs", "section_id", (int)$request->getPost("section_id"));
		Option::set("rodzeta.referenceattribs", "catalog_section_id", (int)$request->getPost("catalog_section_id"));
		//Option::set("rodzeta.referenceattribs", "filter_onchange", $request->getPost("filter_onchange"));
		//Option::set("rodzeta.referenceattribs", "use_options_links", $request->getPost("use_options_links"));

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

<script>

function RodzetaReferenceattribsUpdate($selectDest) {
	var $selectIblock = document.getElementById("iblock_id");
	var iblockId = $selectIblock.value;
	var selectedOption = $selectDest.getAttribute("data-value");
	BX.ajax.loadJSON("/bitrix/admin/rodzeta.referenceattribs/sectionoptions.php?iblock_id=" + iblockId, function (data) {
		var html = ["<option value=''>(выберите раздел)</option>"];
		for (var k in data) {
			var selected = selectedOption == k? "selected" : "";
			html.push("<option " + selected + " value='" + k + "'>" + data[k] + "</option>");
		}
		$selectDest.innerHTML = html.join("\n");
	});
};

</script>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr class="heading">
		<td colspan="2">Настройки для свойств-справочников</td>
	</tr>

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
				"RodzetaReferenceattribsUpdate(document.getElementById('rodzeta-referenceattribs-section-id'));RodzetaReferenceattribsUpdate(document.getElementById('rodzeta-referenceattribs-catalogsection-id'));"
			) ?>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Раздел "Справочники"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<select name="section_id" id="rodzeta-referenceattribs-section-id"
					data-value="<?= Option::get("rodzeta.referenceattribs", "section_id") ?>">
				<option value="">(выберите раздел)</option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Раздел "Каталог"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<select name="catalog_section_id" id="rodzeta-referenceattribs-catalogsection-id"
					data-value="<?= Option::get("rodzeta.referenceattribs", "catalog_section_id", 7) ?>">
				<option value="">(выберите раздел)</option>
			</select>
		</td>
	</tr>

	<?php /*
	<tr class="heading">
		<td colspan="2">Настройки для фильтра</td>
	</tr>
	*/ ?>

	<?php /*
	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Применять фильтр при изменении опций</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="filter_onchange" value="Y" type="checkbox"
				<?= Option::get("rodzeta.referenceattribs", "filter_onchange") == "Y"? "checked" : "" ?>>
		</td>
	</tr>
	*/ ?>

	<?php /*
	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>AJAX-режим</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="use_options_links" value="Y" type="checkbox"
				<?= Option::get("rodzeta.referenceattribs", "use_options_links") == "Y"? "checked" : "" ?>>
		</td>
	</tr>
	*/ ?>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<script>

RodzetaReferenceattribsUpdate(document.getElementById("rodzeta-referenceattribs-section-id"));
RodzetaReferenceattribsUpdate(document.getElementById("rodzeta-referenceattribs-catalogsection-id"));

</script>

<?php

$tabControl->end();
