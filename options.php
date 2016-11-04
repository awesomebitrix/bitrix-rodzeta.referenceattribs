<?php
/***********************************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Referenceattribs;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

$currentIblockId = Option::get("rodzeta.referenceattribs", "iblock_id", 2);

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", array(
	array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_DATA_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_DATA_TAB_TITLE_SET", array(
			"#FILE#" => _FILE_ATTRIBS
		)),
  ),
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_MAIN_TAB_TITLE_SET"),
  ),
));

if ($request->isPost() && \check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.referenceattribs", "iblock_id", (int)$request->getPost("iblock_id"));
		//Option::set("rodzeta.referenceattribs", "section_id", (int)$request->getPost("section_id"));
		Option::set("rodzeta.referenceattribs", "catalog_section_id", (int)$request->getPost("catalog_section_id"));
		//Option::set("rodzeta.referenceattribs", "filter_onchange", $request->getPost("filter_onchange"));
		//Option::set("rodzeta.referenceattribs", "use_options_links", $request->getPost("use_options_links"));

		$errors = CreateCache($request->getPost("attribs"));
		if (!empty($errors["BY_ALIAS"])) {
			\CAdminMessage::showMessage(array(
		    "MESSAGE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_ERROR_ALIAS_DUPLICATES", array(
					"#VALUE#" => implode(", ", $errors["BY_ALIAS"])
				)),
		    "TYPE" => "ERROR",
		  ));
		}

		\CAdminMessage::showMessage(array(
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
	<?= \bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">
			<table width="100%" class="rodzeta-referenceattribs">
				<thead>
					<tr>
						<th></th>
						<th>
							Выводить в разделах
							<div class="rodzeta-referenceattribs-sections-src" style="display:none;">
								<select multiple size="14" style="width:90%;">
									<?php foreach (SectionsTreeList($currentIblockId) as $optionValue => $optionName) { ?>
										<option value="<?= $optionValue ?>"><?= $optionName ?></option>
									<?php } ?>
								</select>
							</div>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php list($attribs) = Config(); foreach (AppendValues($attribs, 10, array_fill(0, 12, null)) as $i => $row) { ?>
						<tr>
							<td>
								<input type="hidden" name="attribs[<?= $i ?>][SECTION_ID]" value="<?= htmlspecialcharsex($row["SECTION_ID"]) ?>">
								<input type="text" placeholder="Код атрибута"
									name="attribs[<?= $i ?>][CODE]"
									value="<?= htmlspecialcharsex($row["CODE"]) ?>"
									size="16">
								<br>
								<input type="text" placeholder="Название"
									name="attribs[<?= $i ?>][NAME]"
									value="<?= htmlspecialcharsex($row["NAME"]) ?>"
									size="16">
								<br>
								<input type="text" placeholder="Сортировка"
									name="attribs[<?= $i ?>][SORT]"
									value="<?= htmlspecialcharsex($row["SORT"]) ?>"
									size="16">
								<br>
								<br>
								<select name="attribs[<?= $i ?>][INPUT_TYPE]" title="Тип поля для фильтра">
									<option value="">CHECKBOX</option>
									<option value="RADIO" <?= $row["INPUT_TYPE"] == "RADIO"? "selected" : "" ?>>RADIO</option>
									<option value="SELECT" <?= $row["INPUT_TYPE"] == "SELECT"? "selected" : "" ?>>SELECT</option>
									<option value="MULTISELECT" <?= $row["INPUT_TYPE"] == "MULTISELECT"? "selected" : "" ?>>MULTISELECT</option>
								</select>
								<br>
								<br>
								<input type="hidden" name="attribs[<?= $i ?>][FILTER]" value="">
								<input type="hidden" name="attribs[<?= $i ?>][COMPARE]" value="">
								<label title="Использовать в фильтре">
									<input type="checkbox"
										name="attribs[<?= $i ?>][FILTER]"
										value="1" <?= !empty($row["FILTER"])? "checked" : "" ?>>&nbsp;Фильтр
								</label>
								<br>
								<label title="Использовать в сравнении">
									<input type="checkbox"
										name="attribs[<?= $i ?>][COMPARE]"
										value="1" <?= !empty($row["COMPARE"])? "checked" : "" ?>>&nbsp;Сравнение
								</label>
								<br>
							</td>
							<td>
								<div class="rodzeta-referenceattribs-sections">
									<input type="text" style="display:none;"
										name="attribs[<?= $i ?>][SECTIONS]" value="<?= htmlspecialcharsex(implode(",", array_keys($row["SECTIONS"]))) ?>">
								</div>
							</td>
							<td nowrap>
								<?php foreach (AppendValues($row["VALUES"], 10, array("", "")) as $n => $v) { ?>
									<input type="text" placeholder="Значение"
										name="attribs[<?= $i ?>][VALUES][<?= $n ?>][NAME]"
										value="<?= htmlspecialcharsex($v["NAME"]) ?>"
										size="25">
									<input type="text" placeholder="Алиас (для ЧПУ)"
										name="attribs[<?= $i ?>][VALUES][<?= $n ?>][ALIAS]"
										value="<?= htmlspecialcharsex($v["ALIAS"]) ?>"
										size="25">
									<br>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<br>
								<br>
							</td>
						<tr>
					<?php } ?>
				</tbody>
			</table>
		</td>
	</tr>

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
				$currentIblockId,
				"iblock_type_id",
				"iblock_id",
				array(
					"MIN_PERMISSION" => "R",
				),
				"",
				"RodzetaReferenceattribsUpdate(document.getElementById('rodzeta-referenceattribs-catalogsection-id'));"
			) ?>
		</td>
	</tr>

	<?php /*
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
	*/ ?>

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

<style>

table.rodzeta-referenceattribs input,
table.rodzeta-referenceattribs select,
table.rodzeta-referenceattribs label {
	margin-bottom: 4px !important;
}

table.rodzeta-referenceattribs td {
	vertical-align: top;
}

</style>

<script>

//RodzetaReferenceattribsUpdate(document.getElementById("rodzeta-referenceattribs-section-id"));
RodzetaReferenceattribsUpdate(document.getElementById("rodzeta-referenceattribs-catalogsection-id"));

BX.ready(function () {
	"use strict";

	//RodzetaSettingsAttribsUpdate();

	var $selectSections = document.querySelectorAll(".rodzeta-referenceattribs-sections");
	var selectSectionsSrc = document.querySelector(".rodzeta-referenceattribs-sections-src").innerHTML;
	for (var i = 0, l = $selectSections.length; i < l; i++) {
		var $sections = $selectSections[i].querySelector("input");

		// append sections selector
		$selectSections[i].innerHTML = $selectSections[i].innerHTML + selectSectionsSrc;
		var $selectSectionsInput = $selectSections[i].querySelector("select");

		$selectSectionsInput.onchange = function (event) {
			// update selected options
			var sectionsIds = [];
			for (var i in event.target.options) {
				if (event.target.options[i].selected) {
					sectionsIds.push(event.target.options[i].value);
				}
			}
			event.target.parentNode.querySelector("input").value = sectionsIds.join(",");
		}

		// init selected options
		var sectionsIds = $sections.value.split(",");
		if (sectionsIds.length > 0) {
			for (var idx in sectionsIds) {
				if (sectionsIds[idx] != "") {
					var $option = $selectSectionsInput.querySelector('[value="' + sectionsIds[idx] + '"]');
					if ($option) {
						$option.selected = true;
					}
				}
			}
		}
	}

});

</script>

<?php

$tabControl->end();
