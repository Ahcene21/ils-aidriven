/**
 * Live preview for the settings that can change without a reload.
 */
( function ( api ) {
	'use strict';

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			var name = document.querySelector( '.brand__name' );

			if ( name ) {
				name.textContent = to;
			}
		} );
	} );

}( window.wp.customize ) );
