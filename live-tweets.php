<?php
/*

Plugin Name: Live Tweets
Version: 0.0.1
Description: Ajax Live Tweets that use Twitter API 1.1
Author: Pigmento
Author URI: http://www.pigmentolab.com
Plugin URI: 
Text Domain: pg_live_tweets
Domain Path:

Copyright 2011  Maicol Zenatti (email : zenatti.maicol@gmail.com) Francesco Benanti (email: benanti@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/**
* Log function
* 
* @since 0.0.1
*/
if ( !function_exists( 'log_me' ) ) {
    function log_me( $message ) {
        if ( WP_DEBUG === true ) {
            if ( is_array( $message ) || is_object( $message ) ) {
                error_log( print_r( $message, true ) );
            }
            else {
                error_log( $message );
            }
        }
    }

}

/**
* Plugin base
*/

if ( !defined( 'PG_LT_PLUGIN_BASE' ) ) {
    define( 'PG_LT_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}

require dirname( __FILE__ ) . '/inc/options.php';
require dirname( __FILE__ ) . '/inc/core.php';

// class instance
$pg_live_tweets = new Pigmento_Live_Tweets( array() );

// base init
$pg_live_tweets -> init();

// init admin utils
if ( is_admin() ) {
    $pg_live_tweets -> init_admin();
}
// // init others utils
// else {
//     $pg_live_tweets -> init_twitter();
// }

?>
