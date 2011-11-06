<?
/**
 * Hooks into action, all and checks it against a list of valid referrers.
 */

function src_check($hook, $action, $return, $params) {
	$url = get_config('url');
	// action => valid referrer (can be array)
	$actions_referers = array(
		'register' => array($url . 'pg/register/', $url . 'account/register.php')
	);

	if (array_key_exists($action, $actions_referers)) {
		src_log();
		$valid = $actions_referers[$action];
		$current = $_SERVER['HTTP_REFERER'];
		if (!is_array($valid)) {
			$valid = array($valid);
		}

		foreach ($valid as $referer) {
			if ($referer == $current) {
				return null;
			}
		}

		//header('HTTP/1.0 301 Moved Permanently');
		//header('Location: http://127.0.0.1');
		//exit;
		die('Invalid referrer for this action. Please contact info@elgg.org for more information.');
	}

	return null;
}

function src_tarpit() {
	$h = fopen('/dev/random', 'rb');

	$len = 8192;
	$sleep = 5;
	$max_time = 60 * 5;

	$wait = 0;

	while ($wait < $max_time) {
		$contents = fread($h, $len);
		echo $contents;
		flush();
		sleep($sleep);
		$wait += $sleep;
	}
}

function src_log() {
        global $CONFIG;

        $log = $CONFIG->dataroot . 'src_log.txt';

        $time = date('r');
	ob_start();
	print_r($_REQUEST);
	$output = ob_get_contents();
	ob_end_clean();
	$ip = $_SERVER['REMOTE_ADDR'];
	$request = $_SERVER['REQUEST_URI'];
	$referer = $_SERVER['HTTP_REFERER'];
	$entry = "$time - $ip - $request - $referer\n$output";

        $fp = fopen($log, 'a+b');
        fputs($fp, $entry);
        fclose($fp);
}


register_plugin_hook('action', 'all', 'src_check');
