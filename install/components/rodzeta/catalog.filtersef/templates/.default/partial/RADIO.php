
<div class="form-field js-filter-param js-filter-param-<?= $v["GROUP"]["ID"] ?>">
	<div class="js-field-radio">
		<label><?= $groupName ?></label>
	  <?php foreach ($v["VALUE"] as $id => $value) {
	  	$checked = !empty($arResult["SELECTED_VALUES"][$id])? "checked" : "";
    ?>
    	<label>
    		<input type="radio" class="js-filter-by-url" <?= $checked ?>
    			name="tmp_filter[<?= $v["GROUP"]["ID"] ?>]"
          data-field-id="<?= $id ?>"
          data-slug="<?= $value["CODE"] ?>"
          value="<?= $id ?>">
       	<span class="js-filter-link"><a href="<?= $v["LINK"][$id] ?>"><?= $value["NAME"] ?></a></span>
      </label>
  	<?php } ?>
	</div>
</div>
