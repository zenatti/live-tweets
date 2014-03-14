<?php

/**
* Live Tweets Core
*/
class Pigmento_Live_Tweets {

    /**
    * Private variables
    */

    // class options
    private $options;

    // class settings
    private $settings;

    // class instance
    private $instance;

    // pages class instance
    private $pages;

    // twitter class instance
    private $twitter;

    // local settings
    private $local_settings = false;

    /**
    * Constructor
    */
    function __construct( $options ) {

        // options
        $this -> options = $options;

        // this class instance
        $this -> instance = $this;

        // settings
        $this -> settings = array(
            'group' => 'pg_live_tweets_settings_group',
            'keys' => array(
                0 => 'oauth_access_token',              // Access token 
                1 => 'oauth_access_token_secret',       // Access token secret
                2 => 'consumer_key',                    // API key
                3 => 'consumer_secret',                 // API secret
                4 => 'screen_name',                     // Screen name
            )
        );

        /**
        * Global paths definition (TODO: local variables conversion)
        */

        if ( !defined( 'PG_LT_THEME_DIR' ) ) {
            define( 'PG_LT_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template() );
        }

        if ( !defined( 'PG_LT_PLUGIN_NAME' ) ) {
            define( 'PG_LT_PLUGIN_NAME', trim( dirname( PG_LT_PLUGIN_BASE ), '/' ) );
        }

        if ( !defined( 'PG_LT_PLUGIN_DIR' ) ) {
            define( 'PG_LT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . PG_LT_PLUGIN_NAME );
        }

        if ( !defined( 'PG_LT_PLUGIN_URL' ) ) {
            define( 'PG_LT_PLUGIN_URL', WP_PLUGIN_URL . '/' . PG_LT_PLUGIN_NAME );
        }

        if ( !defined( 'PG_LT_PLUGIN_LOCALE' ) ) {
            define( 'PG_LT_PLUGIN_LOCALE', PG_LT_PLUGIN_DIR . '/locale' );
        }

        if ( !defined( 'PG_LT_CACHE_DIR' ) ) {
            define( 'PG_LT_CACHE_DIR', ABSPATH . 'wp-content/uploads/live-tweets-cache' );
        }

        /**
        * Add global options to WP
        */

        if ( get_option( PG_LT_VERSION_KEY ) === false ) {
            add_option( PG_LT_VERSION_KEY, PG_LT_VERSION_VAL, '', 'no' );
        }
        else {
            update_option( PG_LT_VERSION_KEY, PG_LT_VERSION_VAL );
        }

        if ( get_option( PG_LT_CACHE_DIR_KEY ) === false ) {
            add_option( PG_LT_CACHE_DIR_KEY, PG_LT_CACHE_DIR, '', 'no' );
        }
        else {
            update_option( PG_LT_CACHE_DIR_KEY, PG_LT_CACHE_DIR );
        }

        /**
        * Create cache folder if not exists
        */

        // check for dir
        if ( !is_dir( PG_LT_CACHE_DIR ) ) {

            // recursive create dir
            mkdir( PG_LT_CACHE_DIR, 0777, true );

        }

    }


    /**
    * Wordpress publiuc actions/filters functions
    */

    /**
    * Add addmin menu pages
    *
    * @since 0.0.1
    */
    public function wp_admin_menu_action() {

        // add the top-level admin menu
        $menu_title = 'Live Tweets';
        $capability = 'manage_options';
        $menu_slug = 'pg_live_tweets_settings';
        $function = array(
            &$this -> pages,
            'pg_admin_settings'
        );
        $page_title = __( 'Live Tweets', 'pg_live_tweets' );
        add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );

        // add submenu page with same slug as parent to ensure no duplicates
        $sub_menu_title = __( 'Settings', 'pg_live_tweets' );
        add_submenu_page( $menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function );

        // now add the submenu page for Help
        $submenu_title = __( 'Help', 'pg_live_tweets' );
        $submenu_slug = 'pg_live_tweets_help';
        $submenu_function = array(
            &$this -> pages,
            'pg_admin_help'
        );
        $submenu_page_title = __( 'Help', 'pg_live_tweets' );
        add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

    }

    /**
    * Add "settings" link to plugins list
    *
    * @since 0.0.1
    */
    public function wp_plugin_action_links_filter( $links, $file ) {
        static $this_plugin;

        if ( !$this_plugin ) {
            $this_plugin = PG_LT_PLUGIN_BASE;
        }

        if ( $file == $this_plugin ) {
            $settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=pg_live_tweets_settings">' . __( 'Settings', 'pg_live_tweets' ) . '</a>';
            array_unshift( $links, $settings_link );
        }

        return $links;
    }

    /**
     *  
     *
     * @since 0.0.1
     */
    public function wp_init_action() {

        // load translation
        load_plugin_textdomain( 'pg_live_tweets', '', PG_LT_PLUGIN_LOCALE );

    }

    /**
     * Sanitize and validate input. accepts an array, return a sanitized array.
     *
     * @since 0.0.1
     */
    public function wp_settings_validate_callback( $input ) {

        // // Our first value is either 0 or 1
        // $input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
        //
        // // Say our second option must be safe text with no HTML tags
        // $input['sometext'] =  wp_filter_nohtml_kses($input['sometext']);
        //

        return $input;
    }

    /**
     * Sanitize settings
     *
     * @since 0.0.1
     */
    public function wp_admin_init_action() {

        // register settings
        register_setting( $this -> settings['group'], $this -> settings['group'], array(
             &$this,
            'wp_settings_validate_callback'
        ));

    }

    /**
     * Init function
     *
     * @since 0.0.1
     */
    function init() {

        // include pages class
        require PG_LT_PLUGIN_DIR . "/inc/pages.php";

        // pages class instance
        $this -> pages = new Pigmento_Live_Tweets_Pages( array( "settings" => $this -> settings ) );

        // include widget class
        require PG_LT_PLUGIN_DIR . "/inc/widget.php";

        // register widget
        add_action( 'widgets_init', function(){

            // add widget
            register_widget( 'Pigmento_Live_Tweets_Widget' );

        });

        // wp init action
        add_action( 'init', array(
            &$this,
            'wp_init_action'
        ));

        // read settings
        $this -> local_settings = get_option( $this -> settings['group'] );

    }

    /**
    * Init admin
    *
    * @since 0.0.1
    */
    function init_admin() {

        // wp admin_menu action
        add_action( 'admin_menu', array(
            &$this,
            'wp_admin_menu_action'
        ));

        // wp plugin_action_links filter
        // add Settings link near plugin deactivate link
        add_filter( 'plugin_action_links', array(
            &$this,
            'wp_plugin_action_links_filter'
        ), 10, 2);

        // wp admin_init action
        // init options registration
        add_action( 'admin_init', array(
            &$this,
            'wp_admin_init_action'
        ));

    }

} // class Pigmento_Live_Tweets

?>
