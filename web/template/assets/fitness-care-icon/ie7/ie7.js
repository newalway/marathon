/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'ftc\'">' + entity + '</span>' + html;
	}
	var icons = {
		'ftc-icon-apple': '&#xe900;',
		'ftc-icon-apple-heart': '&#xe901;',
		'ftc-icon-brain-love': '&#xe902;',
		'ftc-icon-ball': '&#xe903;',
		'ftc-icon-band': '&#xe904;',
		'ftc-icon-bicycle': '&#xe905;',
		'ftc-icon-bike': '&#xe906;',
		'ftc-icon-apple-alarm': '&#xe907;',
		'ftc-icon-burger': '&#xe908;',
		'ftc-icon-calendar': '&#xe909;',
		'ftc-icon-carrot': '&#xe90a;',
		'ftc-icon-chronometer': '&#xe90b;',
		'ftc-icon-chronometer2': '&#xe90c;',
		'ftc-icon-cuttlery': '&#xe90d;',
		'ftc-icon-date': '&#xe90e;',
		'ftc-icon-drugs': '&#xe90f;',
		'ftc-icon-dumbbell': '&#xe910;',
		'ftc-icon-dumbbells': '&#xe911;',
		'ftc-icon-dumbbells3': '&#xe912;',
		'ftc-icon-email': '&#xe913;',
		'ftc-icon-file-apple': '&#xe914;',
		'ftc-icon-medal': '&#xe915;',
		'ftc-icon-fitness-calculator': '&#xe916;',
		'ftc-icon-flexions': '&#xe917;',
		'ftc-icon-gloves': '&#xe918;',
		'ftc-icon-hanging': '&#xe919;',
		'ftc-icon-apple-love': '&#xe91a;',
		'ftc-icon-heart': '&#xe91b;',
		'ftc-icon-heart-alarm': '&#xe91c;',
		'ftc-icon-doctor': '&#xe91d;',
		'ftc-icon-heart2': '&#xe91e;',
		'ftc-icon-kickboxing': '&#xe91f;',
		'ftc-icon-wake-up': '&#xe920;',
		'ftc-icon-running': '&#xe921;',
		'ftc-icon-drugs3': '&#xe922;',
		'ftc-icon-drugs4': '&#xe923;',
		'ftc-icon-capsule': '&#xe924;',
		'ftc-icon-meter': '&#xe925;',
		'ftc-icon-musculous': '&#xe926;',
		'ftc-icon-music-player': '&#xe927;',
		'ftc-icon-musical': '&#xe928;',
		'ftc-icon-musical2': '&#xe929;',
		'ftc-icon-no-smoke': '&#xe92a;',
		'ftc-icon-notes': '&#xe92b;',
		'ftc-icon-oxygenation': '&#xe92c;',
		'ftc-icon-pencil': '&#xe92d;',
		'ftc-icon-phone-contact': '&#xe92e;',
		'ftc-icon-restaurant': '&#xe92f;',
		'ftc-icon-resting': '&#xe930;',
		'ftc-icon-runner': '&#xe931;',
		'ftc-icon-sauce': '&#xe932;',
		'ftc-icon-scale': '&#xe933;',
		'ftc-icon-scale2': '&#xe934;',
		'ftc-icon-shopping-bag': '&#xe935;',
		'ftc-icon-shopping-bag2': '&#xe936;',
		'ftc-icon-skating': '&#xe937;',
		'ftc-icon-shoe': '&#xe938;',
		'ftc-icon-standing': '&#xe939;',
		'ftc-icon-star-medal': '&#xe93a;',
		'ftc-icon-steroids': '&#xe93b;',
		'ftc-icon-sunrise': '&#xe93c;',
		'ftc-icon-swimming-pool': '&#xe93d;',
		'ftc-icon-thin': '&#xe93e;',
		'ftc-icon-thumbs-up': '&#xe93f;',
		'ftc-icon-trophy': '&#xe940;',
		'ftc-icon-truck38': '&#xe941;',
		'ftc-icon-two328': '&#xe942;',
		'ftc-icon-water53': '&#xe943;',
		'ftc-icon-weightlifter1': '&#xe944;',
		'ftc-icon-weightlifter2': '&#xe945;',
		'ftc-icon-weightlifter3': '&#xe946;',
		'ftc-icon-wine57': '&#xe947;',
		'ftc-icon-yin6': '&#xe948;',
		'ftc-icon-yoga13': '&#xe949;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/ftc-icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
