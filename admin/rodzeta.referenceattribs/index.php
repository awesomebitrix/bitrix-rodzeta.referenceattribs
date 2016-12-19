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
	$data = $request->getPostList();
	//Options\Update($request->getPostList());
}

/*
$currentOptions = Options\Select();
$currentOptions["fields"] = array_merge(
	[
		"AUTHOR" => ["AUTHOR", "Ваше имя"],
		"AUTHOR_EMAIL" => ["AUTHOR_EMAIL", "Ваш e-mail"],
		"TEXT" => ["TEXT", "Ваше сообщение"],
		//
		"USER_REGION" => ["USER_REGION", "Регион"],
		"USER_PHONE" => ["USER_PHONE", "Телефон"],
		"USER_SITE" => ["USER_SITE", "Сайт"],
	],
	$currentOptions["fields"]
);
*/
$currentIblockId = Option::get("rodzeta.site", "iblock_content", 1);


?>

<script>

function RodzetaReferenceattribsUpdate($selectDest) {
	var $selectIblock = document.getElementById("iblock_content");
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

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<div class="adm-detail-title">Настройки для свойств-справочников</div>

	<table width="100%">
		<tr>
			<td class="adm-detail-content-cell-l" width="50%">
				<label>Инфоблок содержащий "Справочники"</label>
			</td>
			<td class="adm-detail-content-cell-r" width="50%">
				<?= GetIBlockDropDownListEx(
					$currentIblockId,
					"iblock_type",
					"iblock_content",
					[
						"MIN_PERMISSION" => "R",
					],
					"",
					"RodzetaReferenceattribsUpdate(document.getElementById('rodzeta-referenceattribs-catalogsection-id'));"
				) ?>
			</td>
		</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Раздел "Каталог"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<select name="section_content" id="rodzeta-referenceattribs-catalogsection-id"
					data-value="<?= Option::get("rodzeta.site", "section_content", 1) ?>">
				<option value="">(выберите раздел)</option>
			</select>
		</td>
	</tr>


		<tr>
			<td>
				<input type="text" size="30" name="bitrix24_portal_url"
					value="<?= htmlspecialcharsex($currentOptions["bitrix24"]["portal_url"]) ?>"
					style="width:96%;"
					placeholder="Адрес портала Bitrix24">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="text" size="30" name="bitrix24_login"
					value="<?= htmlspecialcharsex($currentOptions["bitrix24"]["login"]) ?>"
					style="width:96%;"
					placeholder='LOGIN пользователя-"лидогенератора"'>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input name="bitrix24_password" size="30" type="password"
					style="width:96%;"
					readonly
	    		onfocus="this.removeAttribute('readonly')"
	    		value="<?= htmlspecialcharsex($currentOptions["bitrix24"]["password"]) ?>"
	    		placeholder='PASSWORD пользователя-"лидогенератора"'>
			</td>
		</tr>
	</table>

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

<script>

//RodzetaReferenceattribsUpdate(document.getElementById("rodzeta-referenceattribs-section-id"));
RodzetaReferenceattribsUpdate(document.getElementById("rodzeta-referenceattribs-catalogsection-id"));

</script>