<?php

if (!interface_exists('ilog4wp_Logger')) {
	interface ilog4wp_Logger {
		public function log($severity, $logger, $message, exception $ex);
		public function configure();
		public function log_level();
	}
}
