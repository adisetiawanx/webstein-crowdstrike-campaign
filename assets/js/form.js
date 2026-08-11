/**
 * Front end behaviour for the Request a Demo form.
 *
 * Two jobs, both small, both deliberately vanilla. No framework ships to
 * visitors.
 *
 *   1. Carry campaign parameters into the lead. Issue #10.
 *   2. Stop a second submission while the first is in flight. Issue #14.
 *
 * ---------------------------------------------------------------------------
 * 1. Campaign parameters
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

	/* -----------------------------------------------------------------------
	 * Double submission guard, issue #14.
	 *
	 * The form posts normally rather than over AJAX, so an impatient second
	 * click sends a second request and creates a duplicate lead. The mockup is
	 * flat and specifies no submitting state, so this is a build decision.
	 *
	 * The button is disabled on submit and its label changes, which also gives
	 * screen reader users confirmation that something happened. It is only
	 * disabled once the browser has accepted the submission, so a form that
	 * fails native validation does not end up with a dead button.
	 * --------------------------------------------------------------------- */
	document.addEventListener('submit', function (event) {
		var form = event.target;
		if (!form || !form.id || form.id.indexOf('gform_') !== 0) {
			return;
		}

		var button = form.querySelector('input[type="submit"]');
		if (!button || button.disabled) {
			return;
		}

		// Let the submission leave first, then lock the button.
		window.setTimeout(function () {
			button.disabled = true;
			button.dataset.cscLabel = button.value;
			button.value = 'Sending...';
		}, 0);
	});

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
