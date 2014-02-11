<?php

if (!class_exists('log4wp_Logger_WPDB') && interface_exists('ilog4wp_Logger')) {

	class log4wp_Logger_WPDB implements ilog4wp_Logger {

		static $table_name = 'log4wp_wpdb';
		static public $date_time_format = 'Y-m-d H:i:s';

		public function log($severity, $logger, $message, exception $ex = null) {

			if (null !== $ex)
				$message .= "\n" . $ex;

			// http://stackoverflow.com/questions/14206159/decimal-length-for-microtimetrue
			// tl;dr. Use microtime(false) and store the results in a MySQL bigint as millionths of seconds.
			// Otherwise you have to learn all about floating point arithmetic, which is a big fat hairball.

			global $wpdb;
			$timestamp = explode(' ', microtime(false));
			$wpdb->insert($wpdb->prefix . self::$table_name,
				array(
					'severity' => $severity,
					'entry_date_timestamp' => $timestamp[1],
					'entry_date_microtime' => $timestamp[0],
					'logger' => $logger,
					'entry_message' => $message,
				),
				array(
					'%d',
					'%d',
					'%d',
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


		public function can_return_log_entries() {
			return true; // since this is a database, yes
		}


		public function get_log_entries($from_timestamp, $to_timestamp, $severity = null, $logger = NULL) {

			global $wpdb;
			$sql = $wpdb->prepare('select * from ' . $wpdb->prefix . self::$table_name . ' where entry_date_timestamp >= %d and entry_date_timestamp <= %d ',
				$from_timestamp,
				$to_timestamp
			);

			$sql .= ' order by entry_date_timestamp desc, entry_date_microtime desc limit 100';
			//echo $sql;

			do_action('log4wp_debug', 'log4wp', 'get_log_entries sql: ' . $sql);

			$results = $wpdb->get_results($sql);
			for ($i=0; $i < count($results); $i++) {
				$description = 'DEBUG';

				switch ($results[$i]->severity) {
					case 0:
						$description = 'FATAL';
						break;

					case 1:
						$description = 'ERROR';
						break;

					case 2:
						$description = 'WARNING';
						break;

					case 4:
						$description = 'INFO';
						break;

					case 8:
						$description = 'DEBUG';
						break;
				}

				$results[$i]->severity_description = $description;
			}

			return $results;

		}


		public function get_distinct_loggers() {

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
				entry_date_timestamp bigint NOT NULL,
				entry_date_microtime decimal(9,8) NOT NULL,
				logger varchar(100) NOT NULL,
				entry_message longtext NOT NULL,
  				PRIMARY KEY  id (id),
  				INDEX  date_and_logger (entry_date_microtime, logger)
			);";

			dbDelta($sql);

		}
	}
}
