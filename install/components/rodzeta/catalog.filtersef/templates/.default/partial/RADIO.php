
<div class="form-field js-filter-param js-filter-param-<?= $attr["CODE"] ?>">
  <div class="js-field-radio">
    <label><?= $attr["NAME"] ?></label>
    <?php foreach ($attr["VALUES"] as $i => $value) {
      $checked = !empty($arResult["SELECTED_VALUES"][$value["ID"]])? "checked" : "";
    ?>
      <label>
        <input type="radio" class="js-filter-by-url" <?= $checked ?>
          name="tmp_filter[<?= $attr["SECTION_ID"] ?>]"
          data-field-id="<?= $attr["SECTION_ID"] ?>"
          data-slug="<?= $value["ALIAS"] ?>"
          value="<?= $value["ID"] ?>">
        <span class="js-filter-link"><a href="<?= $value["LINK"] ?>"><?= $value["NAME"] ?></a></span>
      </label>
    <?php } ?>
  </div>
</div>
