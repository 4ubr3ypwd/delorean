<?php
/**
 * Cursors (save points).
 *
 * @since 1.0.0
 * @package  aubreypwd\Delorean
 */

namespace aubreypwd\Delorean;

/**
 * Cursors (save points).
 *
 * @author Aubrey Portwood
 * @since 1.0.0
 */
class Cursors {

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'switch_cursor' ) );
		add_action( 'init', array( $this, 'maybe_create_cursor' ) );
	}

	/**
	 * Switch the cursor.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early bail if cursor does not exist.
	 */
	public function switch_cursor() {
		global $wpdb;

		$home = get_home_path();

		// What cursor do they want to goto?
		$cursor = isset( $_GET['cursor'] ) ? absint( $_GET['cursor'] ) : '';

		// Where we store the cursor prefix.
		$cursor_file = "{$home}/.delorean-cursor";

		if ( empty( $cursor ) ) {

			// No cursor, so bail.
			return;
		}

		if ( ! $this->cursor_exists( $cursor ) ) {
			return;
		}

		// Store the cursor.
		file_put_contents( $cursor_file, $cursor );

		// Reload the page they were trying to load...
		wp_redirect( remove_query_arg( 'cursor', $_SERVER['REQUEST_URI'] ) );
		exit;
	}

	/**
	 * Does a cursor exist in the DB?
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $cursor The cursor.
	 * @return boolean        True if a _posts table exists for it.
	 */
	public function cursor_exists( $cursor ) {
		global $wpdb;

		$db            = DB_NAME;
		$cursor_exists = $wpdb->query( "SELECT * FROM information_schema.tables WHERE table_schema = '{$db}' AND table_name = 'delorean_{$cursor}_posts' LIMIT 1;" );

		return 0 === $cursor_exists ? false : true;
	}

	/**
	 * Create a cursor.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early bail when it's not supposed to happen.
	 */
	public function maybe_create_cursor() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		if ( ! isset( $_GET['create-cursor'] ) ) {
			return;
		}

		global $wpdb;
		global $table_prefix;

		if ( stristr( $table_prefix, 'delorean_' ) ) {

			// Never make cursors for cursors.
			return;
		}

		// Get current cursors.
		$cursors = $this->get_cursors();

		// What is this cursor?
		$cursor = count( $cursors ) + 1;

		// Copy the tables.
		$tables = array_merge( $wpdb->tables, $wpdb->global_tables );
		foreach ( $tables as $table ) {
			$target_table = "delorean_{$cursor}_{$table}";
			$source_table = "{$table_prefix}{$table}";
			$wpdb->query( "CREATE TABLE IF NOT EXISTS {$target_table} LIKE {$source_table};" );
			$wpdb->query( "INSERT {$target_table} SELECT * FROM {$source_table};" );
		}

		if ( $this->cursor_exists( $cursor ) ) {

			// Save the new cursor, we created the copy.
			update_option( 'delorean_cursors', array_merge( $cursors, array( $cursor ) ) );
		}
	}

	/**
	 * Get the current cursors.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return array Cursors.
	 */
	public function get_cursors() {
		$cursors = get_option( 'delorean_cursors' );

		if ( ! is_array( $cursors ) ) {
			$cursors = array();
		}

		return $cursors;
	}
}
