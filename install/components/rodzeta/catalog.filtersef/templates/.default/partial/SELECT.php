
<div class="form-field js-filter-param js-filter-param-<?= $v["GROUP"]["ID"] ?>">
	<div class="js-field-select">
		<label><?= $groupName ?></label>
    <select>
  	  <?php foreach ($v["VALUE"] as $id => $value) {
  	  	$selected = !empty($arResult["SELECTED_VALUES"][$id])? "selected" : "";
      ?>
      	<option class="js-filter-by-url" <?= $selected ?>
    			data-field-id="<?= $id ?>"
          data-slug="<?= $value["CODE"] ?>"
          value="<?= $id ?>"><?= $value["NAME"] ?></option>
    	<?php } ?>
    </select>

    <?php foreach ($v["VALUE"] as $id => $value) { ?>
      <a href="<?= $v["LINK"][$id] ?>" style="display:none;"><?= $value["NAME"] ?></a>
    <?php } ?>
	</div>
</div>
