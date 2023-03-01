<?php

App::uses('BaseLog', 'Log/Engine');

/**
 * A Log stream that will write messages directly to logstash http input
 * in json format
 *
 */
class LogstashLog extends BaseLog {

/**
 * Configuration used in this logger engine
 *
 * @var array
 */
	protected $_config = array(
		'host' => null
	);

/**
 * Encodes a message and logs it directly to logstash
 *
 * @param string $type
 * @param string $message
 * @return void
 */
	public function write($type, $message) {
		$log = array(
			'@event.type' => $type,
		);

		if (is_string($message)) {
			$log['@message'] = $message;
		} else {
			$log['@fields'] =json_encode($message);
		}
        
		$log = json_encode($log);
		
		// Ensure utf-8 encoding
		if (mb_detect_encoding($log) !== "UTF-8") {
			$log = utf8_encode($log);
		}

		return $this->_post_async($this->_config['host'],$log);
	}

/**
 * Configures this logger stream
 *
 * @param array $config
 * @return array
 */
	public function config($config = array()) {
		if (empty($config)) {
			return parent::config();
		}

		if (!isset($config['timeout'])) {
			$config['timeout'] = 5;
		}
		return parent::config($config);
	}

/**
 * Creates a connection to send HTTP POST to the URL defined in the configuration
 *
 * @param string $url
 * @param string $body
 * @return boolean
 */
	function _post_async($url, $body)
	{

		try{
			// Connect to server
			$parts=parse_url($url);
			$fp = fsockopen($parts['host'],isset($parts['port'])?$parts['port']:80,$errno, $errstr,$this->_config['timeout']);

			// Build HTTP query             
			$out = "POST ".(isset($parts['path'])?$parts['path']:"/")." HTTP/1.1\r\n";
			$out.= "Host: ".$parts['host']."\r\n";
			$out.= "Content-Type: application/json\r\n";
			$out.= "Content-Length: ".strlen($body)."\r\n";
			$out.= "Connection: Close\r\n\r\n";
			$out.= $body;

			// Send data and close the connection
			fwrite($fp, $out);
			fclose($fp);
			
			return true;

		}catch (Exception $e) {
			return false;
		}
	}

}
