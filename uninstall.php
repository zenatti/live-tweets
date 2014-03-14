<?php

    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit();
    }

    function pg_live_tweets_delete_plugin() {

        delete_option('pg_live_tweets_version');
        delete_option('pg_live_tweets_settings_group');

    }

    pg_live_tweets_delete_plugin();
    
?>