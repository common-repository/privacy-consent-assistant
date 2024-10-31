// Remove Consent Bar
let consentBar      = document.querySelector('#trm-gdpr-consent-bar');
let consentVersions = document.querySelector('#trm-gdpr-versions');

if( consentVersions ){
	var revised  = consentVersions.getAttribute('data-revised');
	var versions = consentVersions.getAttribute('data-versions');
}

function findAncestor(el, search) {
	// Class Search is lowercase, tagName search is UPPERCASE
	while ((el = el.parentElement) && ( !el.classList.contains(search) && el.tagName != search ) );
	return el;
}

function urldecode(url) {
	return decodeURIComponent(url.replace(/\+/g, ' '));
}

function createCORSRequest(method, url) {
	var xhr = new XMLHttpRequest();
	if( 'withCredentials' in xhr ){
		xhr.open(method, url, true);
	} else if( typeof XDomainRequest != 'undefined' ){
		xhr = new XDomainRequest();
		xhr.open(method, url);
	} else {
		xhr = null;
	}

	return xhr;
}

function deleteCookie(name){
	document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function setCookie(name, value, path = '/', expiration = null){
	//Default to 1 Year
	let CookieDate = new Date();
	CookieDate.setFullYear(CookieDate.getFullYear( ) + 1);

	let expires = ' expires=' + CookieDate.toGMTString( ) + ';';

	document.cookie = name + '=' + value + '; path=' + path + ';' + expires;
}

var getCookie = function(name){
	var pair = document.cookie.match(new RegExp(name + '=([^;]+)'));
	return !!pair ? pair[1] : null;
};

function closeTRMGDPRconsent(){
	let cookie   = [];
	cookie.name  = 'trm-privacy-consent';
	cookie.value = '{"close":true,"versions":"'+ versions +'","revised":"'+ revised +'"}';

	/* If the cookie got stuck, we delete it.
	 * Since it's showing again, it means we need to
	 * regain consent anyways.
	 */
	deleteCookie( cookie.name );
	setCookie( cookie.name, urldecode( cookie.value ) );
	
	if( consentBar != null ) consentBar.remove();
}

function display_consent_bar(){
	if( consentBar != null ){
		setTimeout(function(){
			consentBar.setAttribute( 'style', 'height: auto; opacity: 1;' );
		}, 750);
	}
}

// Auto-remove Consent Bar If Cookie incase PHP didn't remove it due to Caching
(function(){
	let consentCookie = getCookie( 'trm-privacy-consent' );

	if( consentCookie != null ){
		// Cookie is set, see what consent values they have
		let expectedValue = '{"close":true,"versions":"'+ versions +'","revised":"'+ revised +'"}';
		if( consentCookie == expectedValue ){
			// Consented to current versions - Remove it entirely.
			if( consentBar != null ) consentBar.remove();
			console.log('Privacy Consent Found: [Versions: '+ versions.replace(/-/g, ', ') +'] [Revised: '+ revised.replace(/-/g, ', ').replace(/_/g, ' ') +']');
		} else {
			// Consented to older versions - Display it.
			console.log( 'Privacy Consent Expired' );
			display_consent_bar();
		}
	} else {
		// Cookie is not set at all - Display it.
		console.log( 'Privacy Consent Not Found' );
		display_consent_bar();
	}
})();

// Dynamically Remove Unwanted Form Consents Notices
var dynamicDelete = document.querySelectorAll('.dynamic-delete');
if( dynamicDelete != null ){
	for( var i = 0, n = dynamicDelete.length; i < n; ++i ){
		dynamicDelete[i].onclick = function(){
			if( confirm( 'Click "OK" to remove this notice.' ) ){
				this.classList.add('deleting');

				var parent = findAncestor(this, 'trm-gdpr-ui'),
					formID = findAncestor(this, 'FORM').getAttribute('id'),
					url	   = parent.getAttribute('site-url'),
					page   = parent.getAttribute('dynamic-delete-page'),
					nonce  = parent.getAttribute('nonce'),
					index  = parent.getAttribute('dynamic-delete-index');

				var xhrURL = url + '?trm_gdpr_method=dynamic-delete&dynamic-page='+page+'&dynamic-index='+index+'&nonce='+nonce;

				if( formID != null )
					xhrURL = xhrURL + '&form_id=' + formID;

				var xhr = createCORSRequest('GET', xhrURL);

				if( !xhr ){
					throw new Error('CORS not supported');
				} else {
					xhr.onload = (function( response ) {
						var json = JSON.parse( response.currentTarget.response );
						this.classList.remove('deleting');

						if( json.status == 200 ){
							console.log( json.message );
							parent.remove();
						} else {
							throw new Error( 'Dynamic Delete Failed. ' + json.message );
						}
					}).bind(this);
					xhr.send();
				}
			}
		};
	}
}