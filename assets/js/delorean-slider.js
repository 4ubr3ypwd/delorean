/**
 * Slider.
 *
 * @since 1.0.0
 * @package  aubreypwd\Delorean
 */

/* globals console, jQuery */
if ( ! window.hasOwnProperty( 'deloreanSlider' ) ) {

	/**
	 * Slider.
	 */
	window.deloreanSlider = ( function( $, pub ) {

		function addQueryArg( s, param, value ) {
			return s += ( s.match( /[\?]/g ) ? '&' : '?' ) + param.toString() + '=' + value.toString(); // 'param=value';
		}

		function init() {
			var $slider = jQuery( '#cursors' );
			var $handle = $( '.ui-slider-handle', $slider );
			var latest  = 'Latest';

			if ( ! $slider.length ) {
				return;
			}

			var cursors = $slider.data( 'cursors' );
			var cursor  = $slider.data( 'cursor' ).toString();

			var start = cursors.length;
			if ( '0' !== cursor.toString() ) {
				start = cursors.indexOf( cursor );
			}

			$slider.slider( {
				range: false,
				value: start,
				min: 0,
				max: cursors.length,
				step: 1,
				stop: function( event, choice ) {
					if ( cursors[ choice.value ] ) {
						window.location.href = addQueryArg( window.location.href, 'cursor', cursors[ choice.value ] );
					} else {
						window.location.href = addQueryArg( window.location.href, 'cursor', '0' );
					}
				},
				create: function() {
					if ( $handle.length && '0' !== cursor.toString() ) {
						$handle.text( cursor );
					} else {
						$handle.text( latest );
					}
				},
				slide: function( event, choice ) {
					if ( $handle.length && cursors[ choice.value ] ) {
						$handle.text( cursors[ choice.value ] );
					} else {
						$handle.text( latest );
					}
				}
			} );
		}

		jQuery( document ).ready( init );
		return pub; // Return public things.
	} ( jQuery, {} ) );
} // End if().
