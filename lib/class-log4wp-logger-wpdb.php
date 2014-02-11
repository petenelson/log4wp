<?php

if (!class_exists('log4wp_Logger_WPDB') && interface_exists('ilog4wp_Logger')) {

	class log4wp_Logger_WPDB implements ilog4wp_Logger {

		static $table_name = 'log4wp_wpdb';

		public function log($severity, $logger, $message, exception $ex = null) {

			if (null !== $ex)
				$message .= "\n" . $ex;

			global $wpdb;
			$wpdb->insert($wpdb->prefix . self::$table_name,
				array(
					'severity' => $severity,
					'entry_date' => date('Y-m-d H:i:s'),
					'logger' => $logger,
					'entry_message' => $message,
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
				)
			);

		}

		public function log_level() {
			// TODO read from config setting
			return 99; // all
		}

		public function configure() {
			// TODO load any config details here
		}

		public function run_activation_hooks() {
			$this->create_db_table();
		}

		function create_db_table() {
			// http://codex.wordpress.org/Creating_Tables_with_Plugins

			// You must put each field on its own line in your SQL statement.
			// You must have two spaces between the words PRIMARY KEY and the definition of your primary key.
			// You must use the key word KEY rather than its synonym INDEX and you must include at least one KEY.
			// You must not use any apostrophes or backticks around field names.

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			global $wpdb;
			$table_name = $wpdb->prefix . self::$table_name;

			$sql = "CREATE TABLE $table_name (
				id int NOT NULL AUTO_INCREMENT,
				severity int NOT NULL,
				entry_date datetime NOT NULL,
				logger varchar(100) NOT NULL,
				entry_message longtext NOT NULL,
  				PRIMARY KEY  id (id),
  				INDEX  date_and_logger (entry_date, logger)
			);";

			dbDelta($sql);

		}
	}
}
