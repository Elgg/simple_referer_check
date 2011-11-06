<?php
/**
 * Hooks into action, all and checks it against a list of valid referrers.
 */

elgg_register_plugin_hook_handler('action', 'all', 'src_check');

/**
 * Check the referer
 *
 * @param string $hook   Hook name
 * @param sting  $action Action name
 * @param bool   $return Whether this action will be processed
 * @return void
 */
function src_check($hook, $action, $return) {
	$url = elgg_get_site_url();

	// action => valid referrer (can be array)
	$actions_referers = array(
		'register' => array($url . 'register'),
	);

	if (array_key_exists($action, $actions_referers)) {
		src_log();

		$valid = $actions_referers[$action];
		if (!is_array($valid)) {
			$valid = array($valid);
		}

		if (!in_array($_SERVER['HTTP_REFERER'], $valid)) {
			die(elgg_echo('src:error', array('info@elgg.org')));
		}
	}

	return null;
}

/**
 * Log this action
 */
function src_log() {

	$log = elgg_get_data_path() . 'src_log.txt';

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

/**
 * Unused tarpit for actions
 */
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
