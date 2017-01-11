window.onload = function() {
	let btnPeoplePhysical = selector('#tipopessoapf');
	let btnPeopleJudiciary = selector('#tipopessoapj');
	let formPeoplePhysical = selector('#formCustomerCreate');
	let messagepeoplephysical = selector('.messagepeoplephysical');

	btnPeoplePhysical.addEventListener('click', function() {
		formPeoplePhysical.classList.add('hidden');
		messagepeoplephysical.classList.add('active');
	});
	
	btnPeopleJudiciary.addEventListener('click', function() {
		messagepeoplephysical.classList.remove('active');
		formPeoplePhysical.classList.remove('hidden');
		formPeoplePhysical.classList.add('active');
		console.log(btnPeopleJudiciary);
	});
};
function selector(element) {
	return document.querySelector(element);
}