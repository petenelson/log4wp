<?php

if (!interface_exists('ilog4wp_Appender')) {
	interface ilog4wp_Appender {
		public function get_appender_name();
		public function log($severity, $logger, $message, exception $ex);
		public function configure();
		public function log_level();
		public function can_return_log_entries();
		public function get_log_entries($from_timestamp, $to_timestamp, $severity, $logger);
		public function get_log_entry($id);
		public function get_distinct_loggers();
		public function get_date_time_format();
	}
}
