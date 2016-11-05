
<div class="form-field js-filter-param js-filter-param-<?= $attr["CODE"] ?>">
	<div class="js-field-select">
		<label><?= $attr["NAME"] ?></label>
    <select>
      <option value=""></option>
  	  <?php foreach ($attr["VALUES"] as $i => $value) {
  	  	$selected = !empty($arResult["SELECTED_VALUES"][$value["ID"]])? "selected" : "";
      ?>
      	<option class="js-filter-by-url" <?= $selected ?>
    			data-field-id="<?= $attr["SECTION_ID"] ?>"
          data-slug="<?= $value["ALIAS"] ?>"
          value="<?= $value["ID"] ?>"><?= $value["NAME"] ?></option>
    	<?php } ?>
    </select>

    <?php foreach ($attr["VALUES"] as $i => $value) { ?>
      <a href="<?= $value["LINK"] ?>" style="display:none;"><?= $value["NAME"] ?></a>
    <?php } ?>
	</div>
</div>
