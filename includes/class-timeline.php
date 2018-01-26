<?php
/**
 * Timeline.
 *
 * @since 1.0.0
 * @package  aubreypwd\Delorean
 */

namespace aubreypwd\Delorean;

/**
 * Timeline.
 *
 * @author Aubrey Portwood
 * @since 1.0.0
 */
class Timeline {

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {
		add_filter( 'admin_footer_text', array( $this, 'timeline' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	public function scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_style( 'jquery-ui-theme-base', plugins_url( 'assets/css/jquery-ui.css', app()->plugin_file ), array(), app()->version() );
		wp_enqueue_script( 'delorean-slider', plugins_url( 'assets/js/delorean-slider.js', app()->plugin_file ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider' ), app()->version(), false );
	}

	/**
	 * Show the timeline.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param string $default The default value for admin_footer_text filter.
	 *
	 * @return string The content.
	 */
	public function timeline( $default ) {
		global $table_prefix;
		global $cursor;
		ob_start();
		?>

		<div id="cursors" data-cursor="<?php echo absint( $cursor ); ?>" data-cursors="<?php echo esc_attr( wp_json_encode( app()->cursors->get_cursors() ) ); ?>">
			<div class="ui-slider-handle"></div>
		</div>
		<span id="cursors-chosen"></span>

		<?php
		return ob_get_clean();
	}

	/**
	 * Whether to use an <a> tag or a <span>.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $cursor The cursor.
	 * @return string         a or span tag.
	 */
	public function tag( $cursor ) {
		if ( app()->cursors->is_cursor( $cursor ) ) {
			return 'span';
		}

		return 'a';
	}

	/**
	 * What link to use for a cursor.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $cursor The cursor.
	 * @return string         # to link to nothing, or the cursor invocation URI.
	 */
	public function link( $cursor ) {
		if ( app()->cursors->is_cursor( $cursor ) ) {
			return '#';
		}

		return esc_url( add_query_arg( 'cursor', $cursor, $_SERVER['REQUEST_URI'] ) );
	}
}
