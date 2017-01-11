window.onload = function() {
	let $inputAreaInterest = document.querySelectorAll('.areaInteresse');
	function areaInterest() {
		$inputAreaInterest.forEach(function(element, i) {
			element.addEventListener('click', function() {
				for(let i = 0; i < $inputAreaInterest.length; i++) {
					$inputAreaInterest[i].classList.remove('required-entry');
				}
			})
		})
	}
	areaInterest();
};