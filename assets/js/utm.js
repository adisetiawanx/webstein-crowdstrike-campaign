/**
 * Carry campaign parameters into the lead. Issue #10.
 *
 * Gravity Forms already prepopulates the hidden UTM fields straight from the
 * query string. This covers the case that misses: the visitor arrives from a QR
 * code or an email with the parameters attached, then reloads or returns to the
 * page without them, and submits. Without this the lead arrives with no source
 * and the campaign cannot be measured.
 *
 * Values are held in sessionStorage, so they last for the visit and no longer.
 * Nothing is written to a cookie, and nothing leaves the browser except through
 * the form the visitor chooses to submit.
 *
 * The field id map comes from PHP via wp_localize_script, because field ids are
 * a property of the form, not something to hard code here.
 */
(function () {
	'use strict';

	if (typeof cscUtm === 'undefined' || !cscUtm.fields) {
		return;
	}

	var STORE = 'csc_utm';
	var keys = Object.keys(cscUtm.fields);

	function readStore() {
		try {
			return JSON.parse(sessionStorage.getItem(STORE) || '{}');
		} catch (e) {
			return {};
		}
	}

	var params = new URLSearchParams(window.location.search);
	var stored = readStore();
	var dirty = false;

	keys.forEach(function (key) {
		var value = params.get(key);
		if (value) {
			// Capped in length. It only ever ends up in an email and an entry.
			stored[key] = value.slice(0, 200);
			dirty = true;
		}
	});

	if (dirty) {
		try {
			sessionStorage.setItem(STORE, JSON.stringify(stored));
		} catch (e) {
			/* Private browsing or storage disabled. Not worth failing over. */
		}
	}

	function fill() {
		keys.forEach(function (key) {
			if (!stored[key]) {
				return;
			}
			var input = document.querySelector('input[name="input_' + cscUtm.fields[key] + '"]');
			if (input && !input.value) {
				input.value = stored[key];
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', fill);
	} else {
		fill();
	}

	// Gravity Forms re-renders the form after an AJAX validation pass.
	document.addEventListener('gform_post_render', fill);
})();
