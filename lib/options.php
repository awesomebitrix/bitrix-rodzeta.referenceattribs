<?php
/*******************************************************************************
 * rodzeta.referenceattribs - Infoblock element reference attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Referenceattribs\Options;

use const Rodzeta\Referenceattribs\CONFIG;

function Update($data) {
	$options = [
		"iblock_content" => $data["iblock_content"],
		"section_content" => $data["section_content"],
	];
	\Encoding\PhpArray\Write(CONFIG . "/options.php", $options);
}

function Select() {
	$fname = CONFIG . "/options.php";
	return is_readable($fname)? include $fname : [
		"iblock_content" => 1,
		"section_content" => 1,
	];
}
