<?php
/**
 * Plugin Name: Monitoring Error
 * Plugin URI: http://it.drivekms.devk
 * Description: Plugin to analyse the PHP logs and check if there are errors or warning.
 * Version: 1.0
 * Author: Andrea Marrano
 * Author URI: http://yourwebsiteurl.com/
 * Domain Path: /languages
 * Text Domain: monitoring-error
 **/

ini_set("error_log", "/srv/www/motorksites/drivekms/htdocs/public/wp-content/plugins/monitoring-error/includes/Moerr/logs/php_error.log");
error_reporting(E_ALL);

/********************************
 * Globals definitions *
 *******************************/

defined( 'ABSPATH' ) || die( 'Error 403: Access Denied/Forbidden!' );
defined( 'HOUR_IN_SECONDS' ) || define( 'HOUR_IN_SECONDS', 3600 );
define( 'MOERR_PLUGIN_DIR', ( function_exists( 'plugin_dir_path' ) ? plugin_dir_path( __FILE__ ) : __DIR__ . '/' ) );

/**
 * Autoloader init
 */
if ( file_exists( MOERR_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once MOERR_PLUGIN_DIR . 'vendor/autoload.php';
}

add_action( 'admin_menu', function () {
	$options = new stdClass();
	$options->name = 'monitoring_options';
	\Moerr\OptionPage::init($options);
} );
