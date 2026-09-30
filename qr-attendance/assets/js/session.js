/**
 * Live session screen: draws the rotating QR code, keeps the roll call up to
 * date, and lets the teacher correct statuses or scan student cards.
 */
( function () {
	'use strict';

	var cfg = window.qraSession;
	var root = document.getElementById( 'qra-session' );
	if ( ! cfg || ! root ) {
		return;
	}

	var POLL_MS = 3000;
	var t = cfg.i18n;
	var $ = function ( sel ) {
		return root.querySelector( sel );
	};

	var qrBox = $( '[data-qra-qr]' );
	var bar = $( '[data-qra-bar]' );
	var countdown = $( '[data-qra-countdown]' );
	var endsEl = $( '[data-qra-ends]' );
	var rollBody = $( '[data-qra-roll]' );
	var filter = $( '[data-qra-filter]' );
	var offline = $( '[data-qra-offline]' );

	var state = { url: '', open: root.classList.contains( 'is-open' ), secondsLeft: 0, rotation: 0, endsIn: null, students: [] };
	var pollTimer = null;

	/* -------------------------------------------------- server calls */

	function request( path, body ) {
		var opts = { credentials: 'same-origin', headers: { 'X-WP-Nonce': cfg.nonce } };
		if ( body ) {
			opts.method = 'POST';
			opts.body = new window.FormData();
			Object.keys( body ).forEach( function ( k ) {
				opts.body.append( k, body[ k ] );
			} );
		}
		return window.fetch( cfg.base + path, opts ).then( function ( res ) {
			return res.json().then( function ( data ) {
				if ( ! res.ok ) {
					throw new Error( ( data && data.message ) || t.failed );
				}
				return data;
			} );
		} );
	}

	function poll() {
		window.clearTimeout( pollTimer );
		request( '/live' )
			.then( function ( data ) {
				offline.hidden = true;
				render( data );
			} )
			.catch( function () {
				offline.hidden = false;
				offline.textContent = t.offline;
			} )
			.then( function () {
				pollTimer = window.setTimeout( poll, POLL_MS );
			} );
	}

	/* -------------------------------------------------- rendering */

	function render( data ) {
		if ( state.open && ! data.open ) {
			// Closed (by timer or elsewhere): reload to swap the toolbar.
			window.location.reload();
			return;
		}
		state.open = data.open;
		state.secondsLeft = data.seconds_left;
		state.rotation = data.rotation;
		state.endsIn = data.ends_in;
		state.students = data.students;

		if ( data.open && data.url !== state.url ) {
			state.url = data.url;
			drawQr( data.url );
		}
		if ( ! data.open ) {
			qrBox.innerHTML = '';
		}

		Object.keys( data.counts ).forEach( function ( status ) {
			var el = root.querySelector( '[data-qra-count="' + status + '"]' );
			if ( el ) {
				el.textContent = data.counts[ status ];
			}
		} );
		$( '[data-qra-checked]' ).textContent = data.counts.present + data.counts.late;
		$( '[data-qra-total]' ).textContent = data.total;

		// Do not rebuild the table under the teacher's cursor.
		if ( ! rollBody.contains( document.activeElement ) ) {
			renderRoll();
		}
		tick();
	}

	function drawQr( url ) {
		var qr = window.qrcode( 0, 'M' );
		qr.addData( url );
		qr.make();
		qrBox.innerHTML = qr.createSvgTag( { cellSize: 10, margin: 40, scalable: true } );
	}

	function methodLabel( method ) {
		return { qr: t.methodQr, card: t.methodCard, manual: t.methodManual, auto: t.methodAuto }[ method ] || '';
	}

	function el( tag, attrs, text ) {
		var node = document.createElement( tag );
		Object.keys( attrs || {} ).forEach( function ( k ) {
			node.setAttribute( k, attrs[ k ] );
		} );
		if ( text !== undefined ) {
			node.textContent = text;
		}
		return node;
	}

	function renderRoll() {
		var q = ( filter.value || '' ).trim().toLowerCase();
		rollBody.innerHTML = '';

		if ( ! state.students.length ) {
			var empty = el( 'tr' );
			empty.appendChild( el( 'td', { colspan: 3 }, t.noStudents ) );
			rollBody.appendChild( empty );
			return;
		}

		// Checked-in students first, most recent on top; then the rest by name.
		var rows = state.students.slice().sort( function ( a, b ) {
			var ra = a.status === 'present' || a.status === 'late' ? 0 : 1;
			var rb = b.status === 'present' || b.status === 'late' ? 0 : 1;
			return ra - rb || ( ra === 0 ? b.ts - a.ts : 0 ) || a.name.localeCompare( b.name );
		} );

		rows.forEach( function ( s ) {
			if ( q && ( s.name + ' ' + s.number ).toLowerCase().indexOf( q ) === -1 ) {
				return;
			}
			var tr = el( 'tr', { class: s.status ? 'is-' + s.status : '' } );

			var who = el( 'td' );
			who.appendChild( el( 'strong', {}, s.name ) );
			if ( s.number ) {
				who.appendChild( el( 'br' ) );
				who.appendChild( el( 'span', { class: 'description' }, s.number ) );
			}
			tr.appendChild( who );

			var when = el( 'td', {}, s.time );
			if ( s.method ) {
				when.appendChild( document.createTextNode( ' ' ) );
				when.appendChild( el( 'span', { class: 'qra-method' }, methodLabel( s.method ) ) );
			}
			tr.appendChild( when );

			var cell = el( 'td' );
			var select = el( 'select', { 'data-user': s.id, class: 'qra-status is-' + ( s.status || 'none' ) } );
			select.appendChild( el( 'option', { value: 'none' }, s.status ? t.clear : t.notRecorded ) );
			Object.keys( cfg.statuses ).forEach( function ( key ) {
				var opt = el( 'option', { value: key }, cfg.statuses[ key ] );
				if ( key === s.status ) {
					opt.selected = true;
				}
				select.appendChild( opt );
			} );
			if ( ! s.status ) {
				select.options[ 0 ].selected = true;
			}
			cell.appendChild( select );
			tr.appendChild( cell );

			rollBody.appendChild( tr );
		} );
	}

	rollBody.addEventListener( 'change', function ( e ) {
		var select = e.target.closest( 'select[data-user]' );
		if ( ! select ) {
			return;
		}
		select.disabled = true;
		request( '/mark', { user_id: select.getAttribute( 'data-user' ), status: select.value } )
			.then( function ( data ) {
				select.blur();
				render( data );
			} )
			.catch( function ( err ) {
				window.alert( err.message );
				select.disabled = false;
			} );
	} );

	filter.addEventListener( 'input', renderRoll );

	/* -------------------------------------------------- countdown */

	function formatDuration( s ) {
		var h = Math.floor( s / 3600 );
		var m = Math.floor( ( s % 3600 ) / 60 );
		var sec = s % 60;
		var mm = ( h && m < 10 ? '0' : '' ) + m;
		return ( h ? h + ':' : '' ) + mm + ':' + ( sec < 10 ? '0' : '' ) + sec;
	}

	function tick() {
		if ( ! state.open ) {
			countdown.textContent = '';
			endsEl.textContent = t.closed;
			bar.style.width = '0';
			return;
		}
		if ( state.rotation ) {
			countdown.textContent = t.newCodeIn.replace( '%d', state.secondsLeft );
			bar.style.width = ( 100 * state.secondsLeft ) / state.rotation + '%';
		} else {
			countdown.textContent = '';
			bar.style.width = '100%';
		}
		endsEl.textContent = null !== state.endsIn ? '· ' + t.endsIn.replace( '%s', formatDuration( state.endsIn ) ) : '';
	}

	window.setInterval( function () {
		if ( ! state.open ) {
			return;
		}
		if ( null !== state.endsIn ) {
			state.endsIn = Math.max( 0, state.endsIn - 1 );
		}
		if ( state.rotation ) {
			state.secondsLeft -= 1;
			if ( state.secondsLeft <= 0 ) {
				state.secondsLeft = state.rotation;
				poll(); // Fetch the next code right away.
				return;
			}
		}
		tick();
	}, 1000 );

	/* -------------------------------------------------- full screen */

	var projector = document.getElementById( 'qra-projector' );
	$( '[data-qra-fullscreen]' ).addEventListener( 'click', function () {
		if ( document.fullscreenElement ) {
			document.exitFullscreen();
		} else if ( projector.requestFullscreen ) {
			projector.requestFullscreen();
		} else {
			projector.classList.toggle( 'is-fullscreen' );
		}
	} );

	/* -------------------------------------------------- student cards */

	var cardsButton = $( '[data-qra-cards]' );
	if ( cardsButton ) {
		var scanner = $( '[data-qra-card-scanner]' );
		var cardResult = scanner.querySelector( '.qra-card-result' );
		var label = $( '[data-qra-cards-label]' );
		var camera = new window.QRACamera( scanner.querySelector( 'video' ), function ( code ) {
			request( '/card', { code: code } )
				.then( function ( data ) {
					cardResult.className = 'qra-card-result is-' + ( data.already ? 'info' : 'success' );
					cardResult.textContent = data.message;
					poll();
				} )
				.catch( function ( err ) {
					cardResult.className = 'qra-card-result is-error';
					cardResult.textContent = err.message;
				} );
		} );
		var scanning = false;

		cardsButton.addEventListener( 'click', function () {
			if ( scanning ) {
				camera.stop();
				scanner.hidden = true;
				label.textContent = t.scanCards;
				scanning = false;
				return;
			}
			scanner.hidden = false;
			cardResult.textContent = '';
			camera
				.start()
				.then( function () {
					scanning = true;
					label.textContent = t.stopScanning;
				} )
				.catch( function ( err ) {
					cardResult.className = 'qra-card-result is-error';
					cardResult.textContent = err.message;
				} );
		} );
	}

	poll();
} )();
