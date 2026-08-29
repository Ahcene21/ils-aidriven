/**
 * Mkadmi — front-end behaviour.
 *
 * Two small jobs: the menu button on narrow screens, and keeping the clocks in
 * the profile band ticking. Both are progressive: the markup they enhance is
 * already correct and readable when this file does not run.
 */
( function () {
	'use strict';

	var strings = window.mkadmiL10n || {};

	/**
	 * The menu button.
	 */
	function setUpMenuToggle() {
		var toggle = document.querySelector( '.menu-toggle' );
		var menu = document.getElementById( 'primary-menu' );

		if ( ! toggle || ! menu ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var open = menu.classList.toggle( 'is-open' );

			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );

			var label = toggle.querySelector( '.menu-toggle__label' );

			if ( label && strings.openMenu && strings.closeMenu ) {
				label.textContent = open ? strings.closeMenu : strings.openMenu;
			}
		} );

		// Escape closes the menu and returns focus to the button that opened it.
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key || ! menu.classList.contains( 'is-open' ) ) {
				return;
			}

			menu.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.focus();
		} );
	}

	/**
	 * The clocks.
	 *
	 * Each clock carries an IANA time zone, so the browser resolves the offset —
	 * including whatever daylight saving happens to be in force today.
	 */
	function setUpClocks() {
		var clocks = document.querySelectorAll( '.clock__time[data-timezone]' );

		if ( ! clocks.length || 'undefined' === typeof Intl || ! Intl.DateTimeFormat ) {
			return;
		}

		var formatters = [];

		Array.prototype.forEach.call( clocks, function ( clock ) {
			var zone = clock.getAttribute( 'data-timezone' );

			try {
				formatters.push( {
					element: clock,
					format: new Intl.DateTimeFormat( 'en-GB', {
						timeZone: zone,
						hour: '2-digit',
						minute: '2-digit',
						second: '2-digit',
						hour12: false
					} )
				} );
			} catch ( error ) {
				// An unknown zone keeps the time the server rendered.
			}
		} );

		if ( ! formatters.length ) {
			return;
		}

		function tick() {
			var now = new Date();

			formatters.forEach( function ( clock ) {
				clock.element.textContent = clock.format.format( now );
			} );
		}

		tick();
		window.setInterval( tick, 1000 );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			setUpMenuToggle();
			setUpClocks();
		} );
	} else {
		setUpMenuToggle();
		setUpClocks();
	}
}() );
