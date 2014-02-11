<?php


require_once plugin_dir_path( __FILE__ ) . 'interface-ilog4wp-logger.php';

if (!class_exists('log4wp') && interface_exists('ilog4wp_Logger')) {

	class log4wp {

		static public $SEVERITY_DEBUG = 8;
		static public $SEVERITY_INFO = 4;
		static public $SEVERITY_WARNING = 2;
		static public $SEVERITY_ERROR = 1;
		static public $SEVERITY_FATAL = 0;

		var $initialized = false;


		public function init() {
			if (!$initialized) {

				// register loggers via apply_filters
				$this->register_loggers();

				// register logging hooks
				foreach (array('debug', 'info', 'warning', 'error', 'fatal') as $severity)
					add_action( 'log4wp_' . $severity, array($this, $severity), $priority = 10, $accepted_args = 3 );

				$initialized = true;
			}
		}

		function log($severity, $logger, $message, exception $ex = null) {

			// loop through all registered loggers
			foreach ($this->loggers as $ilog) {
				// log the message
				$ilog->log($severity, $logger, $message, $ex);
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

		public function get_registered_loggers() {
			return $this->loggers;
		}

		public function register_loggers() {

			if (interface_exists('ilog4wp_Logger')) {

				$loggers = apply_filters( 'log4wp_register_loggers', array() );

				if (isset($loggers) && is_array($loggers)) {

					foreach ($loggers as $ilog) {
						if ($ilog instanceof ilog4wp_Logger)
							$this->loggers[] = $ilog;
					}

				}

			}


		}

	}

}
