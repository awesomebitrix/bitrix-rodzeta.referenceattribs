<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs;

use Bitrix\Main\{Application, Localization\Loc};

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// TODO заменить на определение доступа к редактированию контента
// 	if (!$USER->CanDoOperation("rodzeta.siteoptions"))
if (!$GLOBALS["USER"]->IsAdmin()) {
	//$APPLICATION->authForm("ACCESS DENIED");
  return;
}

Loc::loadMessages(__FILE__);
//Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . ID . "/admin/" . ID . "/index.php");

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

StorageInit();

$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	$errors = Update(FromImport($request->getPost("attribs")));
	/*
	if (!empty($errors["BY_ALIAS"])) {
		\CAdminMessage::showMessage([
	    "MESSAGE" => Loc::getMessage("RODZETA_REFERENCEATTRIBS_ERROR_ALIAS_DUPLICATES", [
				"#VALUE#" => implode(", ", $errors["BY_ALIAS"])
			]),
	    "TYPE" => "ERROR",
	  ]);
	}
	*/
}

$currentOptions = Options\Select();
list($attribs) = Select();

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<textarea name="attribs" style="width:96%;height:260px;"></textarea>

	<ul>
		<li>Справочники разделяются пустой строкой</li>
		<li>Значения справочников разделяются символом ";"</li>
		<li>Кодировка файла UTF-8</li>
	</ul>
	Подробнее см. <a href="<?= URL_ADMIN ?>example.csv">пример</a>

	<?php /*

	<table width="100%" class="rodzeta-referenceattribs">
		<thead>
			<tr>
				<th></th>
				<th>
					Выводить в разделах
					<div class="rodzeta-referenceattribs-sections-src" style="display:none;">
						<select multiple size="14" style="width:96%;">
							<?php foreach (SectionsTreeList($currentOptions["iblock_content"]) as $optionValue => $optionName) { ?>
								<option value="<?= $optionValue ?>"><?= $optionName ?></option>
							<?php } ?>
						</select>
					</div>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach (AppendValues($attribs, 1, array_fill(0, 12, null)) as $i => $row) { ?>
				<tr>
					<td width="5%">
						<input type="hidden" name="attribs[<?= $i ?>][SECTION_ID]" value="<?= htmlspecialcharsex($row["SECTION_ID"]) ?>">
						<input type="text" placeholder="Код атрибута"
							name="attribs[<?= $i ?>][CODE]"
							value="<?= htmlspecialcharsex($row["CODE"]) ?>"
							style="width:96px;">
						<br>
						<input type="text" placeholder="Название"
							name="attribs[<?= $i ?>][NAME]"
							value="<?= htmlspecialcharsex($row["NAME"]) ?>"
							style="width:96px;">
						<br>
						<input type="text" placeholder="Сортировка"
							name="attribs[<?= $i ?>][SORT]"
							value="<?= htmlspecialcharsex($row["SORT"]) ?>"
							style="width:96px;">
						<br>
						<br>
						<select name="attribs[<?= $i ?>][INPUT_TYPE]" title="Тип поля для фильтра" style="width:96px;">
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
					<td width="15%">
						<div class="rodzeta-referenceattribs-sections">
							<input type="text" style="display:none;"
								name="attribs[<?= $i ?>][SECTIONS]" value="<?= htmlspecialcharsex(implode(",", array_keys($row["SECTIONS"]))) ?>">
						</div>
					</td>
					<td width="80%" nowrap>
						<?php foreach (AppendValues($row["VALUES"] ?? [], 5, ["", ""]) as $n => $v) { ?>
							<input type="hidden" name="attribs[<?= $i ?>][VALUES][<?= $n ?>][ID]" value="<?= htmlspecialcharsex($v["ID"]) ?>">
							<input type="text" placeholder="Значение"
								name="attribs[<?= $i ?>][VALUES][<?= $n ?>][NAME]"
								value="<?= htmlspecialcharsex($v["NAME"]) ?>"
								style="width:40%;">
							<input type="text" placeholder="Алиас (для ЧПУ)"
								name="attribs[<?= $i ?>][VALUES][<?= $n ?>][ALIAS]"
								value="<?= htmlspecialcharsex($v["ALIAS"]) ?>"
								style="width:40%;">
							<input type="text" placeholder="Сортировка"
								name="attribs[<?= $i ?>][VALUES][<?= $n ?>][SORT]"
								value="<?= htmlspecialcharsex($v["SORT"]) ?>"
								style="width:60px;">
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

	*/ ?>

</form>

<?php if (0 && $formSaved) { ?>

	<script>
		// close after submit
		top.BX.WindowManager.Get().AllowClose();
		top.BX.WindowManager.Get().Close();
	</script>

<?php } else { ?>

	<script>
		// add buttons for current windows
		BX.WindowManager.Get().SetButtons([
			BX.CDialog.prototype.btnSave,
			BX.CDialog.prototype.btnCancel
			//,BX.CDialog.prototype.btnClose
		]);
	</script>

<?php } ?>

<?php /*
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

*/ ?>