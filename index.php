<?php 
/*
Plugin Name: log4wp
Plugin URI: https://github.com/petenelson/log4wp
Description: Versatile logging plugin for WordPress developers
Version: 0.0.1
Author: Pete Nelson (@GunGeekATX)
Author URI: https://twitter.com/GunGeekATX
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . 'class-log4wp.php' ;

if (class_exists('log4wp') && !function_exists('log4wp_init')) {
	add_action('init', 'log4wp_init');
	function log4wp_init() {
		$log4wp = new log4wp();
		$log4wp->init();
	}
}

