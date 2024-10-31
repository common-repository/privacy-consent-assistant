let menuItems   = document.querySelectorAll('#gdpr a');
let adminPanels = document.querySelectorAll('#gdpr .admin-panel');
menuItems.forEach(function(menuItem){
	menuItem.onclick = function(){
		menuItems.forEach(function(menuItem){
			menuItem.classList.remove('current');
		});

		adminPanels.forEach(function(adminPanel){
			adminPanel.classList.add('hide');
			adminPanel.classList.remove('show');
		});

		this.classList.add('current');

		let target = document.querySelector( this.getAttribute('href') );
		target.classList.add('show');

		return false;
	};
});

let policyPageSelects = document.querySelectorAll('#policy-pages select');
policyPageSelects.forEach(function(select){
	select.onchange = function(){
		let label  = this.parentNode;
		let custom = label.querySelector('[type="text"]');

		if( this.value == 'custom' ){
			custom.classList.add('show');
			custom.classList.remove('hide');
			custom.focus();
		} else {
			custom.classList.add('hide');
			custom.classList.remove('show');
		}
	};
});
