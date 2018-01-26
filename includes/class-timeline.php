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
	public function hooks() {
		add_filter( 'admin_footer_text', array( $this, 'timeline' ) );
	}

	public function timeline() {
		ob_start();
		?>

		<strong><?php esc_html_e( 'Delorean:', 'delorean' ); ?></strong>

		<?php foreach ( app()->cursors->get_cursors() as $cursor ) : ?>
			<?php if ( app()->cursors->cursor_exists( $cursor ) ) : ?>
				<a href="<?php echo add_query_arg( 'cursor', $cursor, $_SERVER['REQUEST_URI'] ); ?>"><?php echo absint( $cursor ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php
		return ob_get_clean();
	}
}
