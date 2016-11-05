
BX.ready(function () {
	"use strict";

	function buildUrl($form) {
		let result = $form.getAttribute("data-category-url");
		for (let $param of $form.querySelectorAll(".js-filter-by-url")) {
			if ($param.checked || $param.selected) {
				result += $param.getAttribute("data-slug") + "/";
			}
		}
		return result;
	}

	function initFilter($form) {
		// replace param link to text
		for (let $param of $form.querySelectorAll(".js-filter-link")) {
			$param.innerHTML = $param.querySelector("a").innerHTML;
		}

		$form.addEventListener("submit", function (e) {
			$form.setAttribute("action", buildUrl($form));
			// clear name from radio inputs
			for (let $param of $form.querySelectorAll(".js-field-radio input")) {
				$param.removeAttribute("name");
			}
			//e.preventDefault();
			//return false;
		});
	}

	// init filter blocks
	let $filters = document.querySelectorAll(".js-catalog-filter");
	for (let $formFilter of $filters) {
	  initFilter($formFilter);
	}
});
