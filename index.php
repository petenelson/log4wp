<?php
/*
Plugin Name: log4wp
Plugin URI: https://github.com/petenelson/log4wp
Description: Versatile logging plugin for WordPress developers
Version: 0.0.3
Author: Pete Nelson (@GunGeekATX)
Author URI: https://twitter.com/GunGeekATX
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// log manager
require_once plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . 'class-log4wp.php' ;

// WP Admin tools
require_once plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . 'class-log4wp-wp-admin.php' ;

// log entry class
require_once plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . 'class-log4wp-logentry.php' ;

// built-in wpdb logger
require_once plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . 'class-log4wp-logger-wpdb.php' ;


// initialize our log manager
// this registers actions for the following:
//		log4wp_debug
//		log4wp_info
//		log4wp_warning
//		log4wp_error
//		log4wp_fatal
// usage example:
// do_action('log4wp_debug', 'MyPluginName', 'plugin init hook finished')
// do_action('log4wp_error', 'MyPluginName', 'exception occured', $exception)

add_action('plugins_loaded', 'log4wp_init');
if (class_exists('log4wp')) {

	if (!function_exists('log4wp_init')) {
		function log4wp_init() {
			$log4wp = new log4wp();
			$log4wp->init();
		}
	}

}


// activation hook to create the table for the built-in loggers
register_activation_hook( __FILE__, 'log4wp_activation' );
if (!function_exists('log4wp_activation')) {

	function log4wp_activation() {
		if (class_exists('log4wp_logger_wpdb')) {
			$log4wp_logger_wpdb = new log4wp_logger_wpdb();
			$log4wp_logger_wpdb->create_db_table();
		}
	}

}


// register the built-in loggers with the log manager
add_filter( 'log4wp_register_loggers', 'log4wp_register_wpdb_logger');
if (!function_exists('log4wp_register_wpdb_logger') && class_exists('log4wp_logger_wpdb')) {
	function log4wp_register_wpdb_logger(array $loggers) {
		$loggers[] = new log4wp_logger_wpdb();
		return $loggers;
	}
}


add_action('admin_init', 'log4wp_wp_admin_init');
add_action('admin_menu', 'log4wp_wp_admin_menu');
if (class_exists('log4wp_WP_Admin')) {

	if (!function_exists('log4wp_wp_admin_init')) {
		function log4wp_wp_admin_init() {
			$log4wp_wp_admin = new log4wp_WP_Admin();
			$log4wp_wp_admin->admin_init();
		}
	}

	if (!function_exists('log4wp_wp_admin_menu')) {
		function log4wp_wp_admin_menu() {
			$log4wp_wp_admin_menu = new log4wp_WP_Admin();
			$log4wp_wp_admin_menu->admin_menu();
		}
	}

}

// add_action('init', 'force_an_error');
// function force_an_error() {
// 	try {
// 		throw new Exception('null reference');
// 	} catch (Exception $e) {
// 		//do_action('log4wp_error', 'force_an_error', 'exception occured', $e);
// 	}
// }