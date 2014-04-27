<?php

class WP_Stream_Connector_log4wp extends WP_Stream_Connector {

	/**
	* Name/slug of the context
	*
	* @var string
	*/
	public static $name = 'log4wp';

	/**
	* Actions this context is hooked to
	*
	* @var array
	*/
	public static $actions = array(
		'log4wp_debug',
		'log4wp_info',
		'log4wp_error',
		'log4wp_warning',
		'log4wp_fatal',
	);


	public static function get_label() {
		return __( 'log4wp', 'stream' );
	}

	public static function get_action_labels() {
		return array(
			'debug' => __( 'Debug', 'stream' ),
			'info' => __( 'Info', 'stream' ),
			'warning' => __( 'Warning', 'stream' ),
			'error' => __( 'Error', 'stream' ),
			'fatal' => __( 'Fatal', 'stream' ),
		);
	}

	public static function get_context_labels() {
		$context_labels = array(
			'severity'           => __( 'Severity', 'stream' ),
		);


		return $context_labels;
	}

	public static function callback_log4wp_debug($logger, $message, exception $ex = null) {
		self::log_event('debug', $logger, $message, $ex);
	}

	public static function callback_log4wp_info($logger, $message, exception $ex = null) {
		self::log_event('info', $logger, $message, $ex);
	}

	public static function callback_log4wp_warning($logger, $message, exception $ex = null) {
		self::log_event('warning', $logger, $message, $ex);
	}

	public static function callback_log4wp_error($logger, $message, exception $ex = null) {
		self::log_event('error', $logger, $message, $ex);
	}

	public static function callback_log4wp_fatal($logger, $message, exception $ex = null) {
		self::log_event('fatal', $logger, $message, $ex);
	}

	static function log_event($severity, $logger, $message, exception $ex = null) {
			self::log( $message, 
			array('logger' => $logger),
			0,
			array('severity' => $severity),
			$user_id = null );

	}

}

