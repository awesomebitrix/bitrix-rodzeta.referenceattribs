
# Модуль Справочники для элемента инфоблока на основе разделов

## Описание

Данный модуль содержит набор функций, который позволяют реализовать характеристики элемента (например товаров) на базе стандартных разделов инфоблоков, а так же использовать данные параметры для реализации ЧПУ-фильтра.
Компонент реализующий вывод блока параметров фильтра с перелинковокой возможных значений и подготовку стандартного фильтра bitrix. Компонент фильтра позволяет использовать его с любыми другими компонентами использующими стандартный фильтр bitrix.

## Описание установки и настройки решения

- создайте и заполните раздел "Справочники", который будет использоваться для хранения характеристик;
- структура раздела "Справочники": список предопределенных значений, характеристики реализуются в виде раздела с подразделами, т.е. раздел - тип значения (например Цвет), его подразделы - значения (например Красный, Желтый, Зеленый);
- разместите компонент фильтра, перед компонентом вывода раздела или другим использующим стандартный фильтр bitrix;
- после изменений в разделе "Справочники" - нажмите в настройке модуля кнопку "Применить настройки".

### Пример использования компонента фильтра со стандартным компонентом "Каталог"

добавить в шаблоне компонента "Каталог" (например в файл components\bitrix\catalog\catalog\section.php) код

    if (!empty($GLOBALS["RODZETA_CATALOG_FILTER"])) {
        $arParams["FILTER_NAME"] = "RODZETA_CATALOG_FILTER";
        $arParams["SHOW_ALL_WO_SECTION"] = "Y";
        $arResult["VARIABLES"]["SECTION_ID"] = null;
        $arResult["VARIABLES"]["SECTION_CODE"] = null;
    }

код должен быть после компонента фильтра, но перед компонентом "bitrix:catalog.section" или другим подобным компонентом,
см. пример bitrix\modules\rodzeta.referenceattribs\examples\section.php

добавить в шаблоне компонента "Каталог" (например в файл components\bitrix\catalog\catalog\bitrix\catalog.section\.default\result_modifier.php)

    if (count($arResult["ITEMS"])) {
        // если есть результат по чпу-фильтру - установить статус OK
        CHTTP::SetStatus("200 OK");
    }

прописать параметр SEF_URL_TEMPLATES в настройках компонента "bitrix:catalog.section"

    "SEF_URL_TEMPLATES" => array(
            "sections" => "",
            "section" => "#SECTION_CODE_PATH#/",
            "element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
    ...

### Пример реализации ЧПУ-фильтра - использование без компонента

c явным заданием значений для фильтрации

    list($arrSefFilter, $currentUrl, $currentSectionId, $selectedSections) =
    \Rodzeta\Referenceattribs\Filter::get(array(
        "catalog", // первый элемент - всегда код раздела "Каталог"
        "red", // значения заданные вручную
        "green"
    ));

или для страниц раздела каталога /catalog/*

    list($arrSefFilter, $currentUrl, $currentSectionId, $selectedSections) =
    \Rodzeta\Referenceattribs\Filter::get($APPLICATION->GetCurPage(false));

    <?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "furniture",
    array(
        ...
        "FILTER_NAME" => "arrSefFilter",
        ...

### Пример для инициализации значений атрибутов в result_modifier.php компонента "Элемент каталога"

    \Rodzeta\Referenceattribs\Utils::init($arResult);

### Пример вывода значений атрибутов в шаблоне компонента "Элемент каталога"

    <?php foreach ($arResult["REFERENCEATTRIBS"] as $groupName => $v) { ?>
        <div>
            <span><?= $groupName ?>:
            </span><?php foreach ($v["VALUE"] as $value) { ?>
                <?= $value["NAME"] ?><?= $value["UF_UNIT"] ?>;
            <?php } ?>
        </div>
    <?php } ?>

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
