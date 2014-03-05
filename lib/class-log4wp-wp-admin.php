<?php
// admin utilities for the log viewer, config, etc
if (!class_exists('log4wp_WP_Admin')) {

	class log4wp_WP_Admin {

		static $VERSION = '2014-02-24-01';
		static $PAGE_VIEW_LOGS = 'log4wp-view-logs';
		var $plugin_dir_url = '';

		public function plugins_loaded($plugin_dir_url) {

			$this->plugin_dir_url = $plugin_dir_url;

			add_action( 'admin_menu', array($this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts' ) );

			// AJAXy stuff
			add_action('wp_ajax_log4wp-view-as-json', array($this, 'handle_ajax_view_as_json'), 99);
		}

		function handle_ajax_view_as_json() {

			header('Content-Type: application/json');

			$appenders = apply_filters( 'log4wp_get_appenders', $_REQUEST['appender'] );
			$entry = null;

			if (false !== $appenders && is_array($appenders) && count($appenders) > 0) {

				$appender_class = $appenders[0]->class_name;

				if (class_exists($appender_class)) ''; {

					$appender = new $appender_class();

					if ($appender->can_return_log_entries()) {
						$entry = $appender->get_log_entry($_REQUEST['id']);
						if (isset($entry) && is_array($entry) && count($entry) > 0) {
							echo $entry[0]->entry_message;
							die();
						}

					}
				}

			}

			echo json_encode('invalid entry id');
			die();
		}


		function admin_enqueue_scripts() {
			wp_enqueue_style( 'log4wp-admin', $this->plugin_dir_url . 'css/log4wp-admin.css' , array(), self::$VERSION);
			wp_enqueue_script( 'log4wp-admin', $this->plugin_dir_url . 'js/log4wp-admin.js' , array('jquery'), self::$VERSION);
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
						$max_entry_length = 300;

						$entries = $appender->get_log_entries($from, $to);
						$date_time_format = $appender->get_date_time_format();
						if ($entries) {
							$i = 0;
							foreach ($entries as $e) {

								$row_classes = array('row-details');
								$row_message_classes = array('row-message');

								if ($i % 2 == 0) {
									$row_classes[] = 'alternate';
									$row_message_classes[] = 'alternate';
								}

								if (strlen($e->entry_message) > $max_entry_length)
									$row_message_classes[] = 'row-large-message';


								// should re-do this at some point so we know's it's really JSON, maybe a separate field
								$json_url = '';
								if (0 === strpos(trim($e->entry_message), '{') || 0 === strpos(trim($e->entry_message), '[')) {
									$json_url = admin_url( 'admin-ajax.php?action=log4wp-view-as-json&appender=' . urlencode($appender->get_appender_name()) . '&id=' . $e->id );
									$row_message_classes[] = 'row-json-message';
								}


								$severity_classes = array('cell-event-severity','cell-event', 'cell-event-severity-' . $e->severity_description);

								echo '<tr class="' . implode(' ', $row_classes) . '">';
								echo '<td class="cell-event-datetime cell-event">' . htmlspecialchars(date($date_time_format, $e->entry_date_timestamp ))  . '</td>';
								echo '<td class="cell-event-logger cell-event">' . htmlspecialchars($e->logger) . '</td>';
								echo '<td class="' . implode(' ', $severity_classes) . '">' . htmlspecialchars($e->severity_description) . '</td>';
								echo '</tr>';

								echo '<tr class="' . implode(' ', $row_message_classes) . '">';
								echo '<td colspan="3" class="cell-event-message">';
								echo '<a href="' . $json_url . '" target="_blank" class="entry-view-as-json">View as JSON</a>';
								echo '<pre>' . str_replace("\n", "<br/>", htmlspecialchars($e->entry_message)) . '</pre>';
								echo '<a href="#" class="entry-view-more">View More</a>';
								echo '</td>';
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
