<?php

/**
* return cache status, true if cache time not expired
*
* @param string $cache_filename     cache file path
* @param int $cache_time_expire     cache time duration in seconds
*
* @since 0.1
*/
function is_valid_cache_time( $cache_filename, $cache_time_expire ) {

    return
    // check for file exists
    file_exists( $cache_filename )
    // check for time elapsed
    && ( time() - filemtime( $cache_filename ) ) <= $cache_time_expire;

}

/**
* save object into cache
*
* @param string $cache_filename     cache file path
* @param object $output             object to cache
*
* @since 0.1
*/
function save_response_to_cache( $cache_filename, $output ) {

    // serialize
    $output = serialize( $output );

    // encrypt
    $output = live_tweet_encrypt( $output, md5($cache_filename) );

    // save to file
    file_put_contents( $cache_filename, $output );

    // update time
    touch( $cache_filename, time() );

}

/**
* get object from cache
*
* @param string $cache_filename     cache file path
*
* @since 0.1
*/
function get_response_from_cache( $cache_filename ) {

    // get content
    $output = file_get_contents( $cache_filename );

    // decrypt
    $output = live_tweet_decrypt( $output, md5($cache_filename) );

    // get, decrypt and return
    return unserialize( $output );

}

/**
* output the content of passed object in json
*
* @param object $output     object to encode and print
*
* @since 0.1
*/
function json_output($output) {

    // set header
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 01 Jan 1990 01:00:00 GMT');
    header('Content-type: application/json');

    // output
    echo json_encode($output);

}

/**
* return a formatted error message
*
* @param string $message        error message
* @param int $code              error code 
* @param int $type              error type eg: error, warning, info, success 
*
* @since 0.1
*/
function get_message($message, $code, $type) {

    // build array
    return array(
        "detail" => $message,
        "code" => $code,
        "type" => $type,
        );

}

/**
* Returns an encrypted & utf8-encoded
*
* @since 0.1
*/
function live_tweet_encrypt($string, $encryption_key) {
    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $string_utf8 = utf8_encode($string);
    return mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, $string_utf8, MCRYPT_MODE_ECB, $iv);
}

/**
* Returns decrypted original string
*
* @since 0.1
*/
function live_tweet_decrypt($string, $encryption_key) {
    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $string_utf8 = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $string, MCRYPT_MODE_ECB, $iv);
    return utf8_decode($string_utf8);
}

?>