<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?php

// $arParams

if (empty($arResult["ITEMS"])) {
  return;
}

?>

<div class="obj-search-form-container">

	<form id="obj-search-form"
	    class="form-inline wrapper-wide js-catalog-filter" action="" method="get"
	    data-category-url="<?= $arResult["CURRENT_SECTION_URL"] ?>"
	    data-category="<?= $arResult["CURRENT_SECTION_ID"] ?>"
	    data-category-query="">

	  <div class="form-container">

			<?php foreach ($arResult["ITEMS"] as $groupName => $v) { ?>

        <div class="form-field js-filter-param js-filter-param-<?= $value["CODE"] ?>">

					<div class="js-field-checkbox">
						<label><?= $groupName ?></label>

					  <?php foreach ($v["VALUE"] as $id => $value) {
					  	$checked = !empty($arResult["SELECTED_VALUES"][$id])? "checked" : "";
		        ?>
			      	<label>
			      		<input type="checkbox" class="js-filter-by-url" <?= $checked ?>
				          data-field-id="<?= $id ?>"
				          data-slug="<?= $value["CODE"] ?>"
				          value="<?= $id ?>">
				        <?php if ($arResult["USE_OPTIONS_LINKS"] == "Y") { ?>
			         		<a href="<?= $v["LINK"][$id] ?>"><?= $value["NAME"] ?></a>
			    			<?php } else { ?>
			    				<?= $value["NAME"] ?>
			    			<?php } ?>
			        </label>
			    	<?php } ?>

					</div>

			  </div>

		  <?php } ?>

	    <div class="form-field">
	      <input class="btn-secondary-white btn-filter-apply" value="Подобрать" type="submit">
	      <?php /* <a href="<?= $arResult["CURRENT_SECTION_URL"] ?>" class="btn-filter-reset btn">Сбросить</a> */ ?>
	    </div>
	  </div>

	  <?php /*
	  <?php if (!empty($arParams["FILTER_NAME"])) { ?>
	    <div class="request-item-button">
	      <input type="button" class="btn-request-filter" value="Заявка на запрос сформированный в фильтре поиска">
	    </div>
	  <?php } ?>
	  */ ?>

	  <?php /*
	  <input type="hidden" class="input-products-sorting-by" name="sortby" value="">
	  <input type="hidden" class="input-products-sorting-d" name="sortd" value="">
	  */ ?>

	</form>

</div>


<?php

echo "<pre>";
print_r($arResult["ITEMS"]);
echo "</pre>";
