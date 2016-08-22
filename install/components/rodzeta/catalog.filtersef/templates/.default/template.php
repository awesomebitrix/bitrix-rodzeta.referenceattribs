<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?php
use Bitrix\Main\Config\Option;

// $arParams

if (empty($arResult["ITEMS"])) {
  return;
}

?>

<div class="obj-search-form-container">

	<form id="<?= $arResult["IS_CATALOG_PAGE"]? "obj-search-form" : "obj-search-form-brief" ?>"
	    class="form-inline wrapper-wide js-catalog-filter" action="" method="get"
	    data-category-url="<?= $arResult["CURRENT_URL"] ?>"
	    data-category="<?= $arResult["CURRENT_SECTION_ID"] ?>"
	    data-category-query="">

	  <div class="form-container">

			<?php foreach ($arResult["ITEMS"] as $groupName => $v) { ?>

        <div class="form-field js-filter-param js-filter-param-<?= $value["CODE"] ?>">

					<div class="js-field-checkbox">
						<label><?= $groupName ?></label>

					  <?php foreach ($v["VALUE"] as $id => $value) {
		        	$checked = isset($arResult["SELECTED"][$id])? "checked" : "";
		        ?>
			      	<label>
			      		<input type="checkbox" class="js-filter-by-url" <?= $selected ?>
				          data-field-id="<?= $id ?>"
				          data-slug="<?= $value["CODE"] ?>"
				          value="<?= $id ?>">
				        <?php if (Option::get("rodzeta.referenceattribs", "use_options_links") == "Y") { ?>
			         		<a href="<?= $value["CURRENT_URL"] ?>"><?= $value["NAME"] ?></a>
			    			<?php } else { ?>
			    				<?= $value["NAME"] ?>
			    			<?php } ?>
			        </label>
			    	<?php } ?>

					</div>

			  </div>

		  <?php } ?>

	    <?php if ($arResult["IS_CATALOG_PAGE"]) { ?>
	      <div class="form-field more-dropdown-field hidden">
	        <a class="more-dropdown-link" href="#">Еще</a>
	      </div>
	    <?php } ?>

	    <div class="form-field">
	      <input class="btn-secondary-white btn-filter-apply" value="Подобрать" type="submit">
	      <?php /* <a href="<?= $arResult["CURRENT_URL"] ?>" class="btn-filter-reset btn">Сбросить</a> */ ?>
	    </div>
	  </div>

	  <?php /*
	  <?php if (!empty($arParams["FILTER_NAME"])) { ?>
	    <div class="request-item-button">
	      <input type="button" class="btn-request-filter" value="Заявка на запрос сформированный в фильтре поиска">
	    </div>
	  <?php } ?>
	  */ ?>

	  <input type="hidden" class="input-products-sorting-by" name="sortby" value="">
	  <input type="hidden" class="input-products-sorting-d" name="sortd" value="">

	</form>

</div>


<?php

echo "<pre>";
print_r($arResult["ITEMS"]);
echo "</pre>";
