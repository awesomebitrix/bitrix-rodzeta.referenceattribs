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
	Options\Update($request->getPostList());
}

$currentOptions = Options\Select();

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

	<table width="100%">
		<tr>
			<td class="adm-detail-content-cell-l" width="30%">
				<label>Инфоблок содержащий "Справочники"</label>
			</td>
			<td class="adm-detail-content-cell-r" width="70%">
				<?= GetIBlockDropDownListEx(
					$currentOptions["iblock_content"],
					"iblock_type",
					"iblock_content",
					[
						"MIN_PERMISSION" => "R",
					],
					"",
					"RodzetaReferenceattribsUpdate(document.getElementById('rodzeta-referenceattribs-catalogsection-id'));RodzetaReferenceattribsUpdate(document.getElementById('rodzeta-referenceattribs-referencessection-id'));"
				) ?>
			</td>
		</tr>

		<tr>
			<td class="adm-detail-content-cell-l" width="30%">
				<label>Раздел "Каталог"</label>
			</td>
			<td class="adm-detail-content-cell-r" width="70%">
				<select name="section_content" id="rodzeta-referenceattribs-catalogsection-id"
						data-value="<?= $currentOptions["section_content"] ?>">
					<option value="">(выберите раздел)</option>
				</select>
			</td>
		</tr>

		<tr>
			<td class="adm-detail-content-cell-l" width="30%">
				<label>Раздел "Справочники"</label>
			</td>
			<td class="adm-detail-content-cell-r" width="70%">
				<select name="section_references" id="rodzeta-referenceattribs-referencessection-id"
						data-value="<?= $currentOptions["section_references"] ?>">
					<option value="">(выберите раздел)</option>
				</select>
			</td>
		</tr>

	</table>

</form>

<?php if ($formSaved) { ?>

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

RodzetaReferenceattribsUpdate(document.getElementById(
	"rodzeta-referenceattribs-catalogsection-id"));
RodzetaReferenceattribsUpdate(document.getElementById(
	"rodzeta-referenceattribs-referencessection-id"));

</script>