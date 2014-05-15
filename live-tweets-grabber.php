<?php

//error_reporting(E_ERROR | E_WARNING | E_PARSE);

require dirname( __FILE__ ) . '/inc/options.php';
require dirname( __FILE__ ) . '/inc/functions.php';

require '../../../wp-load.php';
//require '../../../wp-admin/includes/plugin.php';
require dirname( __FILE__ ) . '/api/TwitterAPIExchange.php';

// date formats params
//$date_formats = array('d', 'D', 'j', 'l', 'N', 'S', 'w', 'z', 'W', 'F', 'm', 'M', 'n', 't', 'L', 'o', 'Y', 'y', 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'e', 'I', 'O', 'P', 'T', 'Z', 'c', 'r', 'U');

// check if plugin is active
if ( !is_plugin_active( 'live-tweets/live-tweets.php' ) ) {
	$response_output['messages'][] = get_message("Plugin not active!", 3001, "error");
	json_output($response_output);
	exit();
}  

// get param count
$count = 10;
if (isset($_REQUEST["count"]) && is_numeric($_REQUEST["count"])) {
	$count = intval($_REQUEST["count"]);
	// check limits
	if ($count <= 0 || $count > 10) {
		$count = 10;
	}
}

// get param purge_cache
$purge_cache = false;
if (isset($_REQUEST["purge"]) && is_numeric($_REQUEST["purge"])) {
	$purge_cache = intval($_REQUEST["purge"]) == 1;
}

// response output
$response_output = array(
	'tweets' => array(),
	'messages' => array(),
	);

// get plugin options
$plugin_options = get_option( 'pg_live_tweets_settings_group' );

// get plugin settings
$cache_dir = get_option( PG_LT_CACHE_DIR_KEY );

// check dir
if($cache_dir === false || empty($cache_dir)) {
	$response_output['messages'][] = get_message("No cache directory found!", 1001, "error");
	json_output($response_output);
	exit();
}

// screen name
$screen_name = $plugin_options['screen_name'];

// prepare twitter methods
$twitter_methods = array(
	'url' => "https://api.twitter.com/1.1/statuses/user_timeline.json",
	'method' => "GET",
	'fields' => "?screen_name=$screen_name&count=$count",
);

// generate cache signature
$cache_signature = json_encode($twitter_methods);

// generate cache key
$cache_filename = sprintf("%s/%s", $cache_dir, md5($cache_signature));

// check for cache (60 seconds)
if (!$purge_cache && is_valid_cache_time($cache_filename, 60)) {

	// get from cache
	$response_output['tweets'] = get_response_from_cache($cache_filename);

	// set message info
	$response_output['messages'][] = get_message("Get from cache!", 0, "info");

}
else {

	// twitter settings
	$twitter_settings = array(
		'oauth_access_token' => $plugin_options['oauth_access_token'],
		'oauth_access_token_secret' => $plugin_options['oauth_access_token_secret'],
		'consumer_key' => $plugin_options['consumer_key'],
		'consumer_secret' => $plugin_options['consumer_secret'],
	);

	// init twitter
	$twitter = new TwitterAPIExchange($twitter_settings);
	$response_string = "";

	// do GET requests
	if ($twitter_methods['method'] == "GET") {
		$response_string = $twitter->setGetfield($twitter_methods['fields'])->buildOauth($twitter_methods['url'], $twitter_methods['method'])->performRequest();
	}
	else if ($twitter_methods['method'] == "POST") {
		$response_string = $twitter->buildOauth($twitter_methods['url'], $twitter_methods['method'])->setPostfields($twitter_methods['fields'])->performRequest();
	}

	// decode
	$response_decoded = json_decode($response_string, $assoc = TRUE);

	// check for errors
	if ($response_decoded["errors"][0]["message"] != "") {
		$response_output['messages'][] = get_message($response_decoded[errors][0]["message"], 2001, "error");
		json_output($response_output);
		exit();
	}

	// check for decoded output
	if (isset($response_decoded)) {
		foreach($response_decoded as $item) {

			// build tweet
			$tweet = array(
				"created_date" => $item['created_at'],
				"created_timestamp" => strtotime($item['created_at']),
				"text" => $item['text'],
				"name" => $item['user']['name'],
				"screen_name" => $item['user']['screen_name'],
				"followers_count" => $item['user']['followers_count'],
				"friends_count" => $item['user']['friends_count'],
				"listed_count" => $item['user']['listed_count'],
			);

			// // add date info
			// foreach ($date_formats as $d) {
			// 	$tweet['date'][] = date($d, $tweet["created_timestamp"]);
			// }

			// build tweet
			$response_output['tweets'][] = $tweet;

		}
	}

	// save to cache
	save_response_to_cache($cache_filename, $response_output['tweets']);

	// set message info
	$response_output['messages'][] = get_message("Saved into cache!", 0, "info");

}

json_output($response_output);

exit();

?>