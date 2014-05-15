<?php

/**
* Adds widget.
*/
class Pigmento_Live_Tweets_Widget extends WP_Widget {

    /**
    * Register widget with WordPress.
    */
    function __construct() {

        // call parent constructor
        parent::__construct(
            'pg_live_tweets',
            __('Live Tweets', 'pg_live_tweets'),
            array( 
                'description' => __( 'Live Tweets', 'pg_live_tweets' ), 
                )
            );

        // check if widget is active
        if( is_active_widget( false, false, $this->id_base, true ) ) { 

            // register widget script
            wp_register_script(
                'live-tweets-script',
                PG_LT_PLUGIN_URL . '/js/live-tweets.min.js',
                array( 'jquery' ),
                '0.1',
                true
            );

            // enqueue widget script
            wp_enqueue_script( 'live-tweets-script' );

        }

    }

    /**
    * Front-end display of widget.
    *
    * @see WP_Widget::widget()
    *
    * @param array $args     Widget arguments.
    * @param array $instance Saved values from database.
    */
    function widget( $args, $instance ) {

        // title filters
        $title = apply_filters( 'widget_title', $instance['title'] );

        // detect container
        $container = isset($args['container']) ? $args['container'] : 'ul';
		
		// detect twitter link
        $twitterlink = isset($instance['twitterlink']) && !empty($instance['twitterlink']) ? $instance['twitterlink'] : '';        

        // detect count
        $count = isset($instance['count']) && !empty($instance['count']) ? $instance['count'] : 10;        

        // echo before tmpl
        echo $args['before_widget'];

        // echo title with tmpl
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // echo container
        echo "<$container data-count=\"$count\" class=\"live-tweet-container\"></$container>";

        // echo follow us
		if(strlen($twitterlink) > 0) {
			echo '<p><a href="' . $twitterlink . '">' . __( 'Follow us on Twitter &raquo;', 'pg_live_tweets' ) . '</a></p>';
		}

        // echo after tmpl
        echo $args['after_widget'];

    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'New title', 'pg_live_tweets' );
        $twitterlink = isset( $instance[ 'twitterlink' ] ) ? $instance[ 'twitterlink' ] : '';
        $count = isset( $instance[ 'count' ] ) ? $instance[ 'count' ] : '10';

        ?>

        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'pg_live_tweets' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
        <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Count:', 'pg_live_tweets' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>">
        </p>
		
		<p>
        <label for="<?php echo $this->get_field_id( 'twitterlink' ); ?>"><?php _e( 'Twitter Link:', 'pg_live_tweets' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'twitterlink' ); ?>" name="<?php echo $this->get_field_name( 'twitterlink' ); ?>" type="text" value="<?php echo esc_attr( $twitterlink ); ?>">
        </p>

        <?php 
    }

    /**
    * Sanitize widget form values as they are saved.
    *
    * @see WP_Widget::update()
    *
    * @param array $new_instance Values just sent to be saved.
    * @param array $old_instance Previously saved values from database.
    *
    * @return array Updated safe values to be saved.
    */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
        $instance['twitterlink'] = ( ! empty( $new_instance['twitterlink'] ) ) ? strip_tags( $new_instance['twitterlink'] ) : '';

        return $instance;
    }

} // class Pigmento_Live_Tweets_Widget

?>