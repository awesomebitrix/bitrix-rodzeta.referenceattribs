
# Модуль Свойства-справочники для ЧПУ-фильтра

## Описание

Данный модуль представляет собой конструктор атрибутов, который позволяет реализовать характеристики элемента (например товаров) на базе стандартных разделов инфоблоков, а так же использовать данные параметры для реализации ЧПУ-фильтра. Содержит компонент реализующий вывод блока параметров фильтра с перелинковкой возможных значений и подготовку стандартного фильтра bitrix. Данный компонент фильтра совместим с любыми другими компонентами использующими стандартный параметр фильтра bitrix. Модуль имеет возможность импорта/экспорта атрибутов-справочников в простом текстовом формате.

**Модуль поддерживает разделение данных на уровне доменов, без необходимости настройки многосайтовости и покупки лицензий на дополнительные сайты.**

## Описание установки и настройки решения

- Задайте настройки инфоблока и его разделов "Каталог" и "Справочники"

- Создайте список необходимых атрибутов-справочников и списки их значений через интерфейс редактирования (кнопка "Свойства-справочники").

- Разместите компонент фильтра, перед компонентом вывода раздела или другим использующим стандартный фильтр bitrix.

- Задайте разделы-справочники для элементов инфоблока стандартным способом.

### Пример использования компонента фильтра со стандартным компонентом "Каталог" ("bitrix:catalog")

См. пример https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/blob/master/examples/section.php#L87

- добавить в файл components\bitrix\catalog\catalog\section.php
```
    if (!empty($GLOBALS["RODZETA_CATALOG_FILTER"])) {
        $arParams["FILTER_NAME"] = "RODZETA_CATALOG_FILTER";
        $arParams["SHOW_ALL_WO_SECTION"] = "Y";
        $arResult["VARIABLES"]["SECTION_ID"] = null;
        $arResult["VARIABLES"]["SECTION_CODE"] = null;
    }
```

- добавить в файл components\bitrix\catalog\catalog\bitrix\catalog.section\.default\result_modifier.php
```
    if (count($arResult["ITEMS"])) {
        // если есть результат по чпу-фильтру - установить статус OK
        CHTTP::SetStatus("200 OK");
    }
```

- прописать параметр SEF_URL_TEMPLATES в настройках компонента "bitrix:catalog.section"
```
    "SEF_URL_TEMPLATES" => array(
            "sections" => "",
            "section" => "#SECTION_CODE_PATH#/",
            "element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
    ...
```

### Пример реализации ЧПУ-фильтра - использование без компонента

c явным заданием значений для фильтрации
```
    list($arrSefFilter, $currentUrl, $currentSectionId, $selectedSections) =
        \Rodzeta\Referenceattribs\Filter([
            "catalog", // первый элемент - всегда код раздела "Каталог"
            "red", // значения заданные вручную
            "green"
        ]);
```

или для страниц раздела каталога /catalog/*
```
    list($arrSefFilter, $currentUrl, $currentSectionId, $selectedSections) =
        \Rodzeta\Referenceattribs\Filter($APPLICATION->GetCurPage(false));

    <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "furniture",
        array(
            ...
            "FILTER_NAME" => "arrSefFilter",
            ...
```

### Пример для инициализации значений атрибутов в result_modifier.php компонента "Элемент каталога"

    \Rodzeta\Referenceattribs\Init($arResult);

### Пример вывода значений атрибутов в шаблоне компонента "Элемент каталога"

```
    <?php foreach ($arResult["PROPERTIES"] as $code => $v) { ?>
        <div>
            <span><?= $v["NAME"] ?>:</span>
            <?= !is_array($v["VALUE"])?
                    $v["VALUE"] : implode(", ", $v["VALUE"]) ?>
            <?= $v["HINT"] ?>
        </div>
    <?php } ?>
```

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
