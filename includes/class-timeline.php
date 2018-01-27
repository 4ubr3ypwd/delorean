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
		add_action( 'admin_notices', array( $this, 'timeline' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Scripts & Styles.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0s
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_style( 'jquery-ui-theme-base', plugins_url( 'assets/css/jquery-ui.css', app()->plugin_file ), array(), app()->version() );
		wp_enqueue_style( 'delorian-slider', plugins_url( 'assets/css/delorean-slider.css', app()->plugin_file ), array( 'jquery-ui-theme-base' ), app()->version() );
		wp_enqueue_script( 'delorean-slider', plugins_url( 'assets/js/delorean-slider.js', app()->plugin_file ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider' ), app()->version(), false );
	}

	/**
	 * Show the timeline.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function timeline() {
		global $table_prefix;
		global $cursor;
		?>

		<div id="delorean">
			<div class="wp-slider" id="cursors" data-cursor="<?php echo absint( $cursor ); ?>" data-cursors="<?php echo esc_attr( wp_json_encode( app()->cursors->get_cursors() ) ); ?>">
				<div class="ui-slider-handle"></div>
			</div>
			<span class="label">Delorean</span>
		</div>

		<?php
	}
}
