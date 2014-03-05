<?php
// admin utilities for the log viewer, config, etc
if (!class_exists('log4wp_WP_Admin')) {

	class log4wp_WP_Admin {

		static $VERSION = '2014-02-24-01';
		static $PAGE_VIEW_LOGS = 'log4wp-view-logs';

		public function plugins_loaded($plugin_dir_url) {
			add_action( 'admin_menu', array($this, 'admin_menu' ) );
		}

		public function admin_init() {

		}

		function admin_enqueue_scripts($plugin_dir_url) {
			wp_enqueue_style( 'log4wp-admin', $plugin_dir_url . 'css/log4wp-admin.css' , array(), self::$VERSION);
		}

		function admin_menu() {
			$this->register_admin_menu();
		}

		function register_admin_menu() {
			add_menu_page(__('View Logs', 'log4wp'), 'log4wp', 'activate_plugins', self::$PAGE_VIEW_LOGS, array($this, 'page_view_logs'));
		}

		public function page_view_logs() {

				$appender_name = '';
				$appender_class = '';

				if (empty($appender_name))
					$appender_name = 'log4wp_wpdb_default';

				// get the appender details
				$appenders = apply_filters( 'log4wp_get_appenders', $appender_name );
				if (false !== $appenders && is_array($appenders) && count($appenders) > 0)
					$appender_class = $appenders[0]->class_name;
				

				echo '
				<h2>log4wp - View Logs</h2>
				<table class="wp-list-table widefat fixed log4wp-page-view-logs" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" id="col-event-date" class="manage-column column-event-date" style="">Date</th>
						<th scope="col" id="col-logger" class="manage-column column-logger" style="">Logger</th>
						<th scope="col" id="col-severity" class="manage-column column-severity" style="">Severity</th>
					</tr>
				</thead>
				<tbody>';

				// get the log entries
				if (class_exists($appender_class)) ''; {

					$appender = new $appender_class();

					if ($appender->can_return_log_entries()) {

						$from = strtotime('-1 day');
						$to = strtotime('now');
						
						$entries = $appender->get_log_entries($from, $to);
						$date_time_format = $appender->get_date_time_format();
						if ($entries) {
							$i = 0;
							foreach ($entries as $e) {
								$severity_classes = array('cell-event-severity','cell-event', 'cell-event-severity-' . $e->severity_description);

								echo '<tr class="' . ($i % 2 == 0 ? 'alternate'  : '') . ' row-details">';
								echo '<td class="cell-event-datetime cell-event">' . htmlspecialchars(date($date_time_format, $e->entry_date_timestamp ))  . '</td>';
								echo '<td class="cell-event-logger cell-event">' . htmlspecialchars($e->logger) . '</td>';
								echo '<td class="' . implode(' ', $severity_classes) . '">' . htmlspecialchars($e->severity_description) . '</td>';
								echo '</tr>';

								echo '<tr class="' . ($i % 2 == 0 ? 'alternate'  : '') . ' row-message">';
								echo '<td colspan="3" class="cell-event-message"><pre>' . str_replace("\n", "<br/>", htmlspecialchars($e->entry_message)) . '</pre></td>';
								echo '</tr>';
								$i++;
							}
						}
					}
				}


				echo '</tbody></table><!-- .log4wp-page-view-logs -->';


		}

	}

}
