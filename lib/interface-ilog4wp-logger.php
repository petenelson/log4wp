<?php

if (!interface_exists('ilog4wp_Logger')) {
	interface ilog4wp_Logger {
		public function log($severity, $logger, $message, exception $ex);
		public function configure();
		public function log_level();
		public function can_return_log_entries();
		public function get_log_entries($from_timestamp, $to_timestamp, $severity, $logger);
		public function get_distinct_loggers();
	}
}
