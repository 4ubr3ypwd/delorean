<?php
/**
 * Go 88mph and set table prefix to load the past.
 *
 * @since  1.0.0
 * @package  aubreypwd\Delorean
 */

global $table_prefix;
$original_table_prefix = $table_prefix;
$delorean_cursor_file  = ABSPATH . '/.cursor';
$cursor                = file_exists( $delorean_cursor_file )
	? (string) intVal( trim( file_get_contents( $delorean_cursor_file ) ) )
	: 0;
$table_prefix          = $cursor ? "cursor_{$cursor}_" : $table_prefix; // @codingStandardsIgnoreLine: Not prohibited in this case.
