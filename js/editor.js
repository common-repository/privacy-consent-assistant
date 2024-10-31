let textArea  = document.querySelector('.post-type-gdpr-policy #poststuff #content');
if( textArea != null ){
	textArea.setAttribute('placeholder', 'Leave blank to use the default policy located at /wp-content/plugins/trm-gdpr-assistant/policies/');
}

let container  = document.querySelector('.post-type-gdpr-policy #wp-content-wrap');
if( container != null ){
	let reference  = container.querySelector('#wp-content-editor-container');
	let notice = document.createElement('div');

	notice.innerHTML = 'Leave blank to use the default policy located at /wp-content/plugins/trm-gdpr-assistant/policies/';
	notice.style.color = '#777';
	notice.style.padding = '10px 0';

	container.insertBefore( notice, reference );
}