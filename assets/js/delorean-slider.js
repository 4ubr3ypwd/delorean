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
		var $slider       = 'undefined';
		var currentCursor = 'undefined';

		/**
		 * Add a query argument to the URL.
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 *
		 * @param  {String} s     The URL.
		 * @param  {String} param The query argument.
		 * @param  {String} value The value for the argument.
		 *
		 * @return {String}       The URL with the value added.
		 */
		function addQueryArg( s, param, value ) {
			return s += ( s.match( /[\?]/g ) ? '&' : '?' ) + param.toString() + '=' + value.toString(); // 'param=value';
		}

		/**
		 * Get the Slider.
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 *
		 * @return {Object} jQuery object of the slider.
		 */
		function slider() {
			if ( 'undefined' === $slider ) {
				$slider  = $( '#cursors' );
			}

			return $slider;
		}

		/**
		 * Setup the Delorean slider.
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 *
		 * @return {undefined} Bail if the slider isn't ready.
		 */
		function setupSlider() {
			if ( ! slider().length ) {

				// No slider?
				return;
			}

			var cursors = slider().data( 'cursors' );
			currentCursor  = slider().data( 'cursor' ).toString();
			var start   = cursors.length;
			if ( '0' !== currentCursor.toString() ) {
				start = cursors.indexOf( currentCursor );
			}

			slider().slider( {
				range: false,
				value: start,
				min: 0,
				max: cursors.length,
				step: 1,
				slide: function( event, choice ) {
					if ( cursors[ choice.value ] ) {
						choice = cursors[ choice.value ];
					} else {
						choice = 0;
					}

					// Hide all tooltips.
					$( '#delorean .tooltips div.tooltip' ).removeClass( 'show' );

					// Get the tooltip we want to show and show it.
					var $tooltip = $( '#delorean .tooltips div[data-cursor="' + choice + '"]' );
					$tooltip.addClass( 'show' );
				},
				stop: function( event, choice ) {
					if ( cursors[ choice.value ] ) {
						choice = cursors[ choice.value ];
					} else {
						choice = 0;
					}

					window.location.href = addQueryArg( window.location.href, 'cursor', choice );
				}
			} );
		}

		/**
		 * Adjust the wrapper.
		 *
		 * The Delorean wrapper spans 100% the width, but that can
		 * end up under some of the help screen buttons.
		 *
		 * This adjusts them
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 */
		function adjustDelorean() {
			var $wrapper = $( '#delorean' );
			if ( ! $wrapper.length ) {

				// No wrapper?
				return;
			}

			var $helpButtons = $( '.screen-meta-toggle' );
			if ( ! $helpButtons.length ) {

				// None, leave it alone.
				return;
			}

			// Reset the wrapper's width again.
			$wrapper.css( { width: '95%' } );

			// Figure out how wide all the buttons are.
			var width = 0;
			$helpButtons.each( function( i, v ) {
				width = width + $( v ).width() + 5;
			} );

			// Make the wrapper 100% the width minus the screen helper buttons.
			$wrapper.css( { width: $wrapper.width() - width } );
		}

		// When the DOM is ready.
		$( document ).ready( setupSlider );
		$( document ).ready( adjustDelorean );
		$( window ).on( 'resize', adjustDelorean );

		// Return public things.
		return pub;
	} ( jQuery, {} ) );
} // End if().
