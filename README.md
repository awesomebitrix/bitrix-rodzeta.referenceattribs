
# Модуль Справочники для элемента инфоблока на основе разделов

## Описание

Данный модуль содержит набор функций, который позволяют реализовать характеристики элемента (например товаров) на базе стандартных разделов инфоблоков, а так же использовать данные параметры для реализации ЧПУ-фильтра.

## Особенности

- список предопределенных значений характеристики реализуются в виде раздела с подразделами;
- хранение настроек характеристик в php массиве, который может кешироватся opcache и не создает лишних нагрузок на БД;
- реализация ЧПУ фильтра.

## Как работает

- создайте и заполните раздел "Справочники", который будет использоваться для хранения характеристик;
- структура раздела "Справочники": раздел - тип значения (например Цвет), его подразделы - значения (например Красный, Желтый, Зеленый);
- после изменений в разделе "Справочники" - нажмите в настройке модуля кнопку "Применить настройки".

### Пример для инициализации значений атрибутов в result_modifier.php компонента "Элемент каталога"

    \Rodzeta\Referenceattribs\Utils::init($arResult);

### Пример использования в шаблоне компонента "Элемент каталога"

    <?php foreach ($arResult["REFERENCEATTRIBS"] as $groupName => $v) { ?>
        <div>
            <span><?= $groupName ?>:
            </span><?php foreach ($v["VALUE"] as $value) { ?>
                <?= $value["NAME"] ?><?= $value["UF_UNIT"] ?>;
            <?php } ?>
        </div>
    <?php } ?>

### Пример реализации ЧПУ-фильтра

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

## Демо сайт

http://villa-mia.ru/

## Тех. поддержка и кастомизация

Оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.referenceattribs/pulls
