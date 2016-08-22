<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?php

// $arParams

if (empty($arResult["ITEMS"])) {
  return;
}

echo "<pre>";
print_r($arResult["ITEMS"]);
echo "</pre>";

?>

<div class="obj-search-form-container">

	<form id="<?= $arResult["IS_CATALOG_PAGE"]? "obj-search-form" : "obj-search-form-brief" ?>"
	    class="form-inline wrapper-wide js-catalog-filter" action="" method="get"
	    data-category-url="<?= $arResult["CURRENT_URL"] ?>"
	    data-category="<?= $arResult["CURRENT_SECTION_ID"] ?>"
	    data-category-query="">

	  <div class="obj-search-top-buttons-container">
	    <div class="obj-search-top-buttons">
	      <a href="javascript:void(0);" data-value="164" class="active">Купить</a>
	      <a class="popup-form" href="#sell-form">Продать</a>
	      <a href="javascript:void(0);" data-value="166">Аренда</a>
	    </div>
	  </div>

	  <div class="form-container">

	    <?php foreach ($arResult["ITEMS"] as $requestParamName => $paramValues) {
	        $field = &$configEntityAttribs[$paramValues["CODE"]]; // TODO move filtering params to component
	        if (empty($field["FILTER"])) {
	          continue;
	        }
	      ?>

	      <div class="form-field js-filter-param js-filter-param-<?= $field["CODE"] ?>">

	        <?php /*
	        <span class="js-filter-unit"><?= !empty($field["UNIT"])?
	          (" (" . $field["UNIT"] . ")") : "" ?></span>
	          */ ?>

	        <?php include "product_filter_input_" . basename($field["TYPE"]) . ".php" ?>

	      </div>

	      <?php //var_dump($requestParamName, $paramValues, $_REQUEST[$requestParamName]); ?>

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
