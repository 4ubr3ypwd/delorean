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
	 * Prefix prefix.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $prefix = 'cursor';

	/**
	 * The option in the options table.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $option = 'cursors';

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'switch_cursor' ) );
		add_action( 'init', array( $this, 'create_cursor' ) );
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

		// What cursor do they want to goto?
		$cursor = isset( $_GET['cursor'] ) ? absint( $_GET['cursor'] ) : '';

		// Where we store the cursor prefix.
		$home        = get_home_path();
		$cursor_file = "{$home}/.cursor";

		// 0 or nothing set.
		if ( 0 === $cursor ) {

			// Use default base site.
			unlink( $cursor_file );
		} elseif ( $this->cursor_exists( $cursor ) ) {

			// They want a cursor (that physically exists) so set the cursor.
			file_put_contents( $cursor_file, $cursor );
		} else {

			// No cursor is being requested, bail.
			return;
		}

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
		$cursor_exists = $wpdb->query( "SELECT * FROM information_schema.tables WHERE table_schema = '{$db}' AND table_name = '{$this->prefix}_{$cursor}_posts' LIMIT 1;" );

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
	public function create_cursor() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		if ( ! isset( $_GET['create-cursor'] ) ) {
			return;
		}

		global $table_prefix;

		if ( stristr( $table_prefix, 'delorean_' ) ) {
			wp_die( esc_html__( 'Sorry, but you cannot create a cursor while viewing one.', 'delorean' ) );
		}

		// Get current cursors.
		$cursors = $this->get_cursors();

		// What is this cursor?
		$cursor = count( $cursors ) + 1;

		// Make a copy of the tables.
		// $this->copy_tables_using_sql( $cursor );
		$this->copy_tables_using_dump( $cursor );

		if ( $this->cursor_exists( $cursor ) ) {
			$this->add_cursor( $cursor );
		}
	}

	/**
	 * Copy tables.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $cursor The cursor for the tables to create.
	 */
	private function copy_tables_using_sql( $cursor ) {
		global $wpdb;
		global $table_prefix;

		// Copy the tables.
		$tables = array_merge( $wpdb->tables, $wpdb->global_tables );
		foreach ( $tables as $table ) {
			$target_table = "{$this->prefix}_{$cursor}_{$table}";
			$source_table = "{$table_prefix}{$table}";
			$wpdb->query( "CREATE TABLE IF NOT EXISTS {$target_table} LIKE {$source_table};" );
			$wpdb->query( "INSERT {$target_table} SELECT * FROM {$source_table};" );
		}
	}

	/**
	 * Copy tables using MYSQL dump and import.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $cursor Cursor.
	 */
	private function copy_tables_using_dump( $cursor ) {
		global $wpdb;
		global $table_prefix;

		$db_name     = DB_NAME;
		$db_password = DB_PASSWORD;
		$db_user     = DB_USER;

		// Copy the tables.
		$tables = array_merge( $wpdb->tables, $wpdb->global_tables );
		foreach ( $tables as $table ) {
			$dump_dir = WP_CONTENT_DIR . '/delorean/dumps';

			if ( ! file_exists( $dump_dir ) ) {
				mkdir( WP_CONTENT_DIR . '/delorean' );
				mkdir( WP_CONTENT_DIR . '/delorean/dumps' );
			}

			$source_table = "{$table_prefix}{$table}";
			$dump_file = "{$dump_dir}/{$source_table}.sql";
			$cursor_prefix = "{$this->prefix}_{$cursor}_";

			// Dump the file.
			$dump = "mysqldump -u {$db_user} -p{$db_password} {$db_name} {$source_table} > {$dump_file}";

			// Replace the old prefix with the new.
			$replace = "sed -i 's/{$table_prefix}/{$cursor_prefix}/g' {$dump_file}";

			// Import the new tables.
			$import = "mysql -u {$db_user} -p{$db_password} {$db_name} < $dump_file";

			// Run the commands.
			shell_exec( "$dump && $replace && $import" );
		}
	}

	/**
	 * Where do we store our created cursors?
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return string The file we store all our cursors in.
	 */
	public function cursor_file() {
		return ABSPATH . '/.cursors';
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
		if ( ! file_exists( $this->cursor_file() ) ) {
			return array();
		}

		$cursors = file_get_contents( $this->cursor_file() );
		$cursors = explode( ',', $cursors );
		$cursors_exist = array();

		foreach ( $cursors as $cursor ) {
			if( $this->cursor_exists( $cursor ) ) {
				$cursors_exist = array_merge( $cursors_exist, array( $cursor ) );
			}
		}

		return $cursors_exist;
	}

	/**
	 * Add a cursor.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $cursor The cursor to add to the .cursors file.
	 */
	public function add_cursor( $cursor ) {
		$cursors = array_merge( $this->get_cursors(), array( absint( $cursor ) ) );
		file_put_contents( $this->cursor_file(), implode( ',', $cursors ) );
	}

	/**
	 * Are we already on a cursor?
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param string $for_cursor For what cursor.
	 * @return boolean           True if we are.s
	 */
	public function is_cursor( $for_cursor ) {
		global $cursor;
		return absint( $cursor ) === absint( $for_cursor );
	}
}
