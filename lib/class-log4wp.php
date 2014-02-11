<?php 
if (!class_exists('log4wp')) {
	
	class log4wp {

		static public $SEVERITY_DEBUG = 0;
		static public $SEVERITY_INFO = 1;
		static public $SEVERITY_WARNING = 2;
		static public $SEVERITY_ERROR = 3;
		static public $SEVERITY_FATAL = 4;

		public function init() {
			// register logging hooks
			foreach (array('debug', 'info', 'warning', 'error', 'fatal') as $severity)
				add_action( 'log4wp_' . $severity, array($this, $severity), $priority = 10, $accepted_args = 3 );
		}

		function log($args) {

			// TODO loop through all resgistered loggers

			// log the message

		}

		public function debug($logger, $message, exception $ex) {

		}

		public function info($logger, $message, exception $ex) {
			
		}

		public function warning($logger, $message, exception $ex) {
			
		}

		public function fatal($logger, $message, exception $ex) {
			
		}

	}
}
