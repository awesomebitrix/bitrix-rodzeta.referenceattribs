
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
        <?php if ($arResult["USE_OPTIONS_LINKS"] == "Y") { ?>
       		<a href="<?= $v["LINK"][$id] ?>"><?= $value["NAME"] ?></a>
  			<?php } else { ?>
  				<?= $value["NAME"] ?>
  				<a href="<?= $v["LINK"][$id] ?>" style="display:none;"><?= $value["NAME"] ?></a>
  			<?php } ?>
      </label>
  	<?php } ?>
	</div>
</div>
