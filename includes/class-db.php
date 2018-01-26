<?php
/**
 *
 *
 * @since 1.0.0
 * @package  aubreypwd\Delorean
 */

namespace aubreypwd\Delorean;

/**
 *
 *
 * @author Aubrey Portwood
 * @since 1.0.0
 */
class DB {
	public function hooks() {
		add_action( 'admin_init', array( $this, 'set_cursor' ) );
		add_action( 'init', array( $this, 'create_cursor' ) );
	}

	public function set_cursor() {
		global $table_prefix;
		global $wpdb;

		$home = get_home_path();

		// What cursor do they want to goto?
		$cursor = isset( $_GET['cursor'] ) ? $_GET['cursor'] : '';

		// Where we store the cursor prefix.
		$cursor_file = "{$home}/delorean.cursor";

		if ( empty( $cursor ) ) {

			// No cursor, so bail.
			return;
		}

		$db            = DB_NAME;
		$cursor_exists = $wpdb->query( " SELECT * FROM information_schema.tables WHERE table_schema = '{$db}' AND table_name = '{$cursor}posts' LIMIT 1;" );
		if ( 0 === $cursor_exists ) {

			// This cursor no longer exists.
			return;
		}

		// Store the cursor.
		file_put_contents( $cursor_file, $cursor );

		// Reload the page they were trying to load...
		wp_redirect( remove_query_arg( 'cursor', $_SERVER['REQUEST_URI'] ) );
		exit;
	}


	public function create_cursor() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		global $wpdb;
		global $table_prefix;

		// Get current cursors.
		$cursors = get_option( 'delorean_cursors' );
		if ( ! is_array( $cursors ) ) {
			$cursors = array();
		}

		// What is this cursor?
		$cursor = count( $cursors ) + 1;

		$tables = array_merge( $wpdb->tables, $wpdb->global_tables );
		foreach ( $tables as $table ) {
		//	$wpdb->query( "CREATE TABLE IF NOT EXISTS delorean_{$cursor}_{$table} LIKE {$table_prefix}{$table};" );
			//$wpdb->query( "INSERT delorean_{$cursor}_{$table} SELECT * FROM {$table_prefix}{$table};" );
		}

		// Save the new cursor.
		update_option( 'delorean_cursors', array_merge( $cursors, array( $cursor ) ) );
	}
}
