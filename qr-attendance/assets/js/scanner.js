/**
 * [qra_scanner]: the student scans the teacher's code from inside the site.
 */
( function () {
	'use strict';

	var cfg = window.qraScanner || {};
	var l10n = window.qraCameraL10n || {};

	function setup( root ) {
		var wrap = root.querySelector( '.qra-video-wrap' );
		var video = root.querySelector( 'video' );
		var button = root.querySelector( '[data-qra-toggle]' );
		var result = root.querySelector( '.qra-result' );
		var camera = new window.QRACamera( video, submit );
		var active = false;

		function show( type, message ) {
			result.className = 'qra-result' + ( type ? ' is-' + type : '' );
			result.textContent = message || '';
		}

		function start() {
			show( '', '' );
			wrap.hidden = false;
			camera
				.start()
				.then( function () {
					active = true;
					button.textContent = l10n.stopCamera;
				} )
				.catch( function ( err ) {
					wrap.hidden = true;
					show( 'error', err.message );
				} );
		}

		function stop() {
			camera.stop();
			active = false;
			wrap.hidden = true;
			button.textContent = l10n.startCamera;
		}

		function submit( code ) {
			camera.pause();
			show( 'working', cfg.working );

			var body = new window.FormData();
			body.append( 'code', code );

			window
				.fetch( cfg.endpoint, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'X-WP-Nonce': cfg.nonce },
					body: body,
				} )
				.then( function ( res ) {
					return res.json().then( function ( data ) {
						return { ok: res.ok, data: data };
					} );
				} )
				.then( function ( r ) {
					if ( r.ok && r.data.ok ) {
						stop();
						show( r.data.already ? 'info' : 'success', r.data.message + ( r.data.course ? ' — ' + r.data.course : '' ) );
						button.textContent = cfg.again;
					} else {
						show( 'error', ( r.data && r.data.message ) || cfg.failed );
						camera.resume();
					}
				} )
				.catch( function () {
					show( 'error', cfg.failed );
					camera.resume();
				} );
		}

		button.addEventListener( 'click', function () {
			if ( active ) {
				stop();
			} else {
				start();
			}
		} );
	}

	document.querySelectorAll( '[data-qra-scanner]' ).forEach( setup );
} )();
