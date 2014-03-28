<?php

require_once plugin_dir_path( __FILE__ ) . 'interface-ilog4wp-appender.php';

if (!class_exists('log4wp') && interface_exists('ilog4wp_Appender')) {

	class log4wp {

		static public $SEVERITY_DEBUG = 8;
		static public $SEVERITY_INFO = 4;
		static public $SEVERITY_WARNING = 2;
		static public $SEVERITY_ERROR = 1;
		static public $SEVERITY_FATAL = 0;

		static $TABLE_NAME_APPENDERS = 'log4wp_appenders';

		var $initialized = false;
		var $appenders = array();


		public function plugins_loaded() {
			if (!$this->initialized) {

				// register appenders via apply_filters
				$this->register_appenders();

				// register logging hooks
				foreach (array('debug', 'info', 'warning', 'error', 'fatal') as $severity)
					add_action( 'log4wp_' . $severity, array($this, $severity), $priority = 10, $accepted_args = 3 );

				// filters for getting appender details
				add_filter('log4wp_get_appenders', array($this, 'get_appenders'));

				$initialized = true;
			}
		}


		function log($severity, $logger, $message, exception $ex = null) {

			// when logging...

			// 1) get all appenders
			// 2) get loggers assigned to the appenders for the level that is being logged
			// 3) log the message to the appender


			// loop through all registered loggers
			if (isset($this->appenders))
			foreach ($this->appenders as $iappender) {
				// log the message
				$iappender->log($severity, $logger, $message, $ex);
			}

		}


		public function debug($logger, $message, exception $ex = null) {
			$this->log(self::$SEVERITY_DEBUG, $logger, $message, $ex);
		}


		public function info($logger, $message, exception $ex = null) {
			$this->log(self::$SEVERITY_INFO, $logger, $message, $ex);
		}


		public function warning($logger, $message, exception $ex = null) {
			$this->log(self::$SEVERITY_WARNING, $logger, $message, $ex);
		}


		public function error($logger, $message, exception $ex = null) {
			$this->log(self::$SEVERITY_ERROR, $logger, $message, $ex);
		}


		public function fatal($logger, $message, exception $ex = null) {
			$this->log(self::$SEVERITY_FATAL, $logger, $message, $ex);
		}


		public function get_registered_appender() {
			return $this->appenders;
		}

		public function get_appenders($appender_name) {
			global $wpdb;
			$table_name = $wpdb->prefix . self::$TABLE_NAME_APPENDERS;
			$results = $wpdb->get_results($wpdb->prepare("select * from $table_name where appender_name = '%s'", $appender_name));
			return $results;
		}


		function verify_appender_db_entry($appender_name, $class_name) {
			
			$appenders = $this->get_appenders($appender_name);
			if (false !== $appenders && count($appenders) == 0) {
				global $wpdb;
				$table_name = $wpdb->prefix . self::$TABLE_NAME_APPENDERS;

				$wpdb->query(
					$wpdb->prepare("insert into $table_name(appender_name, class_name) values('%s', '%s')", $appender_name, $class_name)
				);
			}

		}


		public function register_appenders() {

			if (interface_exists('ilog4wp_Appender')) {

				$appenders = apply_filters( 'log4wp_register_appenders', array() );

				if (isset($appenders) && is_array($appenders)) {

					foreach ($appenders as $ilog_appender) {
						if ($ilog_appender instanceof ilog4wp_Appender) {
							$this->appenders[] = $ilog_appender;
							$this->verify_appender_db_entry($ilog_appender->get_appender_name(), get_class($ilog_appender));
						}
					}

				}

			}

		}


		public function run_activation_hooks() {
			$this->create_db_tables();
		}

		
		function create_db_tables() {
			// http://codex.wordpress.org/Creating_Tables_with_Plugins

			// You must put each field on its own line in your SQL statement.
			// You must have two spaces between the words PRIMARY KEY and the definition of your primary key.
			// You must use the key word KEY rather than its synonym INDEX and you must include at least one KEY.
			// You must not use any apostrophes or backticks around field names.

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			global $wpdb;
			$table_name = $wpdb->prefix . self::$TABLE_NAME_APPENDERS;

			$sql = "CREATE TABLE $table_name (
				id int NOT NULL AUTO_INCREMENT,
				appender_name varchar(50) NOT NULL,
				class_name varchar(150) NOT NULL,
  				PRIMARY KEY  id (id)
			);";

			dbDelta($sql);

		}


	}

}
