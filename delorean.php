<?php
/**
 * Plugin Name: Delorean
 * Description: At 88MPH you can take your site back to the future.
 * Version:     1.0.0
 * Author:      aubreypwd
 * Author URI:
 * Text Domain: delorean
 * Network:     False
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since       1.0.0
 * @package     aubreypwd\Delorean
 */

// Our namespace.
namespace aubreypwd\Delorean;

// Require the App class.
require_once 'includes/class-app.php';

// Create a global variable for the app, it's namespaced, don't worry.
$app = null;

/**
 * Create/Get the App.
 *
 * @author Aubrey Portwood
 * @since  1.0.0
 *
 * @return App The App.
 */
function app() {
	global $app;

	if ( null === $app ) {

		// Create the app and go!
		$app = new App( __FILE__ );

		// Attach our other classes.
		$app->attach();

		// Run any hooks.
		$app->hooks();
	}

	return $app;
}

// Wait until WordPress is ready, then go!
add_action( 'plugins_loaded', 'aubreypwd\Delorean\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
