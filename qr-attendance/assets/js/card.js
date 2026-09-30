/**
 * [qra_my_card]: draw the student's personal QR code.
 */
( function () {
	'use strict';

	document.querySelectorAll( '[data-qra-code]' ).forEach( function ( el ) {
		var qr = window.qrcode( 0, 'M' );
		qr.addData( el.getAttribute( 'data-qra-code' ) );
		qr.make();
		el.innerHTML = qr.createSvgTag( { cellSize: 8, margin: 32, scalable: true } );
	} );
} )();
