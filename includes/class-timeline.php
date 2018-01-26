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
		ob_start();
		?>

		<code style="display: inline-block; padding: 10px; border-radius: 3px; position: fixed; bottom: 20px; right: 20px; z-index: 999999999;">
			<a href="<?php echo esc_url( add_query_arg( 'cursor', 0, $_SERVER['REQUEST_URI'] ) ); ?>">0</a>

			<?php foreach ( app()->cursors->get_cursors() as $cursor ) : ?>
				<?php if ( app()->cursors->cursor_exists( $cursor ) ) : ?>
					<<?php echo wp_kses_post( $this->tag( $cursor ) ); ?> href="<?php echo esc_url( $this->link( $cursor ) ); ?>"><?php echo absint( $cursor ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</code>

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
