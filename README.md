# Logstash log stream for CakePHP#


## Requirements ##

* CakePHP 2.x
* PHP 5.3+
* Composer

## Installation ##

The only installation method supported by this plugin is by using composer. Just add this to your composer.json configuration:

	{
	  "require" : {
		"jasanchez/cakephp-logstash": "master"
	  }
	}

### Enable plugin

You need to enable the plugin your `app/Config/bootstrap.php` file:

    CakePlugin::load('Logstash');

Finally add a new logging stream in the same file:

	CakeLog::config('debug', array(
		'engine' => 'Logstash.LogstashLog',
		'types' => array('notice', 'info', 'debug','warning', 'error', 'critical', 'alert', 'emergency'),
		'host' => 'http://complete-url', // Set it to the real host works with udp too
		'timeout' => 5 // Connection timeout
	));
