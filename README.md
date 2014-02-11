log4wp
======

WordPress logging plugin, similar to [log4net](http://logging.apache.org/log4net/)/[http://logging.apache.org/log4j/2.x/](log4j)


## Usage/Examples

In your WordPress plugin, call do_action and pass it the appropriate tag and logger.
```php
do_action('log4wp_debug', 'MyPluginName', 'This is a debug message');
do_action('log4wp_info', 'MyPluginName', 'This is an info message');
do_action('log4wp_warning', 'MyPluginName', 'This is a warning message');
do_action('log4wp_error', 'MyPluginName', 'This is an error message');
```

You can also log exceptions, which will include the full stack trace
```php
try {
	// operation here that may throw an error
} catch (Exception $e) {
	do_action('log4wp_error', 'MyPluginName', 'An exception occured', $e);
}
```

TODO: In the log viewer, you can view log entries for your pluging by severity and date range

TODO: You can configure specific loggers by severity


## Roadmap
* WP Admin - viewer for WPDB database logger
* WP Admin - configure loggers
* impliment built-in email logger

## Revision History

##### v0.0.2 Feb 10, 2014
* Implemented WordPress database logger

##### v0.0.1 Feb 10, 2014
* Initial commit