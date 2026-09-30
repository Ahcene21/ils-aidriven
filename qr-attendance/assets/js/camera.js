/**
 * Camera QR reader shared by the student scanner and the teacher's card scanner.
 *
 * Uses the browser's native BarcodeDetector where available and falls back to
 * jsQR on a downscaled canvas otherwise.
 */
( function () {
	'use strict';

	var l10n = window.qraCameraL10n || {};
	var SCAN_EVERY_MS = 150;
	var REPEAT_AFTER_MS = 4000;
	var MAX_SIDE = 640;

	function QRACamera( video, onCode ) {
		this.video = video;
		this.onCode = onCode;
		this.stream = null;
		this.running = false;
		this.paused = false;
		this.detector = null;
		this.canvas = document.createElement( 'canvas' );
		this.ctx = this.canvas.getContext( '2d', { willReadFrequently: true } );
		this.lastCode = '';
		this.lastAt = 0;
		this.lastScan = 0;
		this.busy = false;
	}

	QRACamera.prototype.start = function () {
		var self = this;

		if ( ! window.isSecureContext ) {
			return Promise.reject( new Error( l10n.insecure ) );
		}
		if ( ! navigator.mediaDevices || ! navigator.mediaDevices.getUserMedia ) {
			return Promise.reject( new Error( l10n.noCamera ) );
		}

		return navigator.mediaDevices
			.getUserMedia( { video: { facingMode: { ideal: 'environment' } }, audio: false } )
			.then( function ( stream ) {
				self.stream = stream;
				self.video.srcObject = stream;
				self.video.setAttribute( 'playsinline', '' );
				return self.video.play();
			} )
			.then( function () {
				if ( 'BarcodeDetector' in window ) {
					try {
						self.detector = new window.BarcodeDetector( { formats: [ 'qr_code' ] } );
					} catch ( e ) {
						self.detector = null;
					}
				}
				self.running = true;
				self.paused = false;
				self.loop();
			} )
			.catch( function ( err ) {
				self.stop();
				if ( err && ( 'NotAllowedError' === err.name || 'SecurityError' === err.name ) ) {
					throw new Error( l10n.denied );
				}
				if ( err && ( 'NotFoundError' === err.name || 'OverconstrainedError' === err.name ) ) {
					throw new Error( l10n.noCamera );
				}
				throw err;
			} );
	};

	QRACamera.prototype.stop = function () {
		this.running = false;
		if ( this.stream ) {
			this.stream.getTracks().forEach( function ( track ) {
				track.stop();
			} );
		}
		this.stream = null;
		this.video.srcObject = null;
	};

	QRACamera.prototype.pause = function () {
		this.paused = true;
	};

	QRACamera.prototype.resume = function () {
		this.paused = false;
		this.lastCode = '';
	};

	QRACamera.prototype.loop = function () {
		var self = this;
		if ( ! self.running ) {
			return;
		}
		window.requestAnimationFrame( function ( now ) {
			if ( ! self.paused && ! self.busy && now - self.lastScan > SCAN_EVERY_MS && self.video.readyState >= 2 ) {
				self.lastScan = now;
				self.busy = true;
				self.detect()
					.then( function ( value ) {
						if ( value ) {
							self.emit( value );
						}
					} )
					.catch( function () {} )
					.then( function () {
						self.busy = false;
					} );
			}
			self.loop();
		} );
	};

	QRACamera.prototype.detect = function () {
		var self = this;
		if ( self.detector ) {
			return self.detector.detect( self.video ).then(
				function ( codes ) {
					return codes.length ? codes[ 0 ].rawValue : '';
				},
				function () {
					// Some platforms expose the API without QR support: fall back for good.
					self.detector = null;
					return '';
				}
			);
		}
		return Promise.resolve( self.detectWithJsQR() );
	};

	QRACamera.prototype.detectWithJsQR = function () {
		if ( ! window.jsQR ) {
			return '';
		}
		var w = this.video.videoWidth;
		var h = this.video.videoHeight;
		if ( ! w || ! h ) {
			return '';
		}
		var scale = Math.min( 1, MAX_SIDE / Math.max( w, h ) );
		w = Math.round( w * scale );
		h = Math.round( h * scale );
		this.canvas.width = w;
		this.canvas.height = h;
		this.ctx.drawImage( this.video, 0, 0, w, h );
		var image = this.ctx.getImageData( 0, 0, w, h );
		var code = window.jsQR( image.data, w, h, { inversionAttempts: 'dontInvert' } );
		return code ? code.data : '';
	};

	QRACamera.prototype.emit = function ( value ) {
		var now = Date.now();
		if ( value === this.lastCode && now - this.lastAt < REPEAT_AFTER_MS ) {
			return;
		}
		this.lastCode = value;
		this.lastAt = now;
		if ( navigator.vibrate ) {
			navigator.vibrate( 80 );
		}
		this.onCode( value );
	};

	window.QRACamera = QRACamera;
} )();
