<?php
/**
 * Configuration hack.
 *
 * @since  1.0.0
 * @package  aubreypwd\Delorean
 */

global $table_prefix;
$delorean_cursor_file = ABSPATH . '/.delorean-cursor';
$cursor               = file_exists( $delorean_cursor_file )
	? (string) intVal( trim( file_get_contents( $delorean_cursor_file ) ) )
	: false;
$table_prefix         = $cursor ? "delorean_{$cursor}_" : $table_prefix; // @codingStandardsIgnoreLine: Not prohibited in this case.
