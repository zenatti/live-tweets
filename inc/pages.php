<?php

/**
* Live Tweets Pages
*/
class Pigmento_Live_Tweets_Pages {

    /**
    * Private viriables
    */

    // class options
    private $options;

    // this class instance
    private $instance;

    /**
    * Constructor
    */
    function __construct( $options ) {

        // options
        $this -> options = $options;

        // this class instance
        $this -> instance = $this;

    }

    /**
     * get admin settings page
     *
     * @since 0.1
     */
    function pg_admin_settings() {

        // check permissions
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        ?>

            <div class="wrap pg_live_tweets">
                <h2><?php printf( "%s - %s", __( 'Live Tweets', 'pg_live_tweets' ), __( 'Settings', 'pg_live_tweets' ) ); ?></h2>
                <form method="post" action="options.php">
                    <?php settings_fields( $this -> options['settings']['group'] ); ?>
                    <?php $options = get_option( $this -> options['settings']['group'] ); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Access token', 'pg_live_tweets' ); ?></th>
                            <td>
                            <input size="60" type="text" name="<?php echo $this -> options['settings']['group']; ?>[<?php echo $this -> options['settings']['keys'][0]; ?>]" value="<?php echo $options[$this -> options['settings']['keys'][0]]; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Access token secret', 'pg_live_tweets' ); ?></th>
                            <td>
                            <input size="60" type="text" name="<?php echo $this -> options['settings']['group']; ?>[<?php echo $this -> options['settings']['keys'][1]; ?>]" value="<?php echo $options[$this -> options['settings']['keys'][1]]; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'API key', 'pg_live_tweets' ); ?></th>
                            <td>
                            <input size="60" type="text" name="<?php echo $this -> options['settings']['group']; ?>[<?php echo $this -> options['settings']['keys'][2]; ?>]" value="<?php echo $options[$this -> options['settings']['keys'][2]]; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'API secret', 'pg_live_tweets' ); ?></th>
                            <td>
                            <input size="60" type="text" name="<?php echo $this -> options['settings']['group']; ?>[<?php echo $this -> options['settings']['keys'][3]; ?>]" value="<?php echo $options[$this -> options['settings']['keys'][3]]; ?>" />
                            </td>
                        </tr>
                    </table>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Screen name (without @)', 'pg_live_tweets' ); ?></th>
                            <td>
                            <input size="30" type="text" name="<?php echo $this -> options['settings']['group']; ?>[<?php echo $this -> options['settings']['keys'][4]; ?>]" value="<?php echo $options[$this -> options['settings']['keys'][4]]; ?>" />
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'pg_live_tweets' ); ?>" />
                    </p>
                </form>
            </div>

        <?php

    }

    /**
     * get admin help page
     *
     * @since 0.1
     */
    function pg_admin_help() {

        // check permissions
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        ?>
        <div class="wrap pg_live_tweets">
            <h2><?php printf( "%s - %s", __( 'Live Tweets', 'pg_live_tweets' ), __( 'Help', 'pg_live_tweets' ) ); ?></h2>
        </div>
        <?php

    }

} // Pigmento_Live_Tweets_Pages

?>
