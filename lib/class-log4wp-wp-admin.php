<?php
// admin utilities for the log viewer, config, etc
if (!class_exists('log4wp_WP_Admin')) {

	class log4wp_WP_Admin {

		public function admin_init() {

		}

		public function admin_menu() {
			$this->register_admin_menu();
		}

		function register_admin_menu() {
			add_menu_page(__('View Logs', 'log4wp'), 'log4wp', 'activate_plugins', 'log4wp-view-logs', array(&$this, 'page_view_logs'));

		}

		public function page_view_logs() {


				echo '
				<h2>log4wp - View Logs</h2>
				<table class="wp-list-table widefat fixed log4wp-page-view-logs" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" id="col-event-date" class="manage-column column-event-date" style="">Date</th>
						<th scope="col" id="col-logger" class="manage-column column-logger" style="">Logger</th>
						<th scope="col" id="col-severity" class="manage-column column-severity" style="">Severity</th>
						<th scope="col" id="col-severity" class="manage-column column-message" style="">Message</th>
					</tr>
				</thead>
				<tbody>';

				// get the log entries
				if (class_exists('log4wp_Logger_WPDB')) {
					$log4wp_logger_wpdb = new log4wp_Logger_WPDB();


					$from = strtotime('-1 day');
					$to = strtotime('now');

					$entries = $log4wp_logger_wpdb->get_log_entries($from, $to);
					if ($entries) {
						$i = 0;
						foreach ($entries as $e) {
							echo '<tr class="' . ($i % 2 == 0 ? 'alternate'  : '') . '">';
							echo '<td>' . htmlspecialchars(date(log4wp_Logger_WPDB::$date_time_format, $e->entry_date_timestamp ))  . '</td>';
							echo '<td>' . htmlspecialchars($e->logger) . '</td>';
							echo '<td>' . htmlspecialchars($e->severity_description) . '</td>';
							echo '<td><pre>' . str_replace("\n", "<br/>", htmlspecialchars($e->entry_message)) . '</pre></td>';
							echo '</tr>';
							$i++;
						}
					}
				}


				echo '</tbody></table><!-- .log4wp-page-view-logs -->';


		}

	}

}
