<?php
/*
Plugin Name: Recently on Flickr Widget
Plugin URI: http://mandiwise.com/project/recently-on-flickr-widget/
Description: A basic Flickr widget that allows you to display a specified number of your most recent Flickr photos in your sidebar.
Version: 1.0.1
Author: Mandi Wise
Author URI: http://mandiwise.com/
Author Email: hello@mandiwise.com
Text Domain: recently-on-flickr-widget-locale
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/mandiwise/recently-on-flickr-widget/

The "Recently on Flickr Widget" was forked and extensively refactored as a plugin from an example orginally posted on Wptuts+ http://wp.tutsplus.com/.

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

class RO_Flickr extends WP_Widget {

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is deactivated
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Define the widget id, class, and description
		parent::__construct(
			'recently-on-flickr-id',
			__( 'Recently on Flickr', 'recently-on-flickr-locale' ),
			array(
				'classname'  => 'recently-on-flickr-class',
				'description' => __( 'Show off your most recent Flickr photos.', 'recently-on-flickr-locale' )
			)
		);

		// Register site styles
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		// get transient based on widget ID
		$output = get_transient( 'flickr_widget_photos' . $widget_id );

		if ( $output === false ) {

			$title = apply_filters( 'widget_title', $instance['title'] );
			if ( empty( $title ) ) {
				$title = false;
			}

			$flickr_id = isset( $instance['flickr_id'] ) ? $instance['flickr_id'] : '';
			$flickr_api_key = isset( $instance['flickr_api_key'] ) ? $instance['flickr_api_key'] : '';
			$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : '';
			$thumb_size = isset( $instance['thumb_size'] ) ? esc_attr( $instance['thumb_size'] ) : '';
			$refresh_feed = isset( $instance['refresh_feed'] ) ? absint( $instance['refresh_feed'] ) : '';

			include_once( plugin_dir_path( __FILE__ ) . 'includes/phpFlickr/phpFlickr.php' );
			$f = new phpFlickr( $flickr_api_key );

			if ( !empty( $flickr_id ) ) {
				$output = $before_widget;

				if( $title ){
					$output .= $before_title;
					$output .= $title;
					$output .= $after_title;
				}

				$person = $f->people_findByUsername( $flickr_id );
				$photos_url = $f->urls_getUserPhotos( $person['id'] );
				$photos = $f->people_getPublicPhotos( $person['id'], NULL, NULL, $number );

				$output .= '<ul class="flickr-photos">';
				foreach ( (array)$photos['photos']['photo'] as $photo ) {
					$output .= '<li>';
					$output .= "<a href=$photos_url$photo[id]>";
					$output .= "<img border='0' alt='$photo[title]' " . "src=" . $f->buildPhotoURL( $photo, "$thumb_size" ) . ">";
					$output .= '</a></li>';
				}
				$output .= '</ul><div class="clear"></div>';

				$output .= $after_widget;

				set_transient( 'flickr_widget_photos' . $widget_id, $output, $refresh_feed * MINUTE_IN_SECONDS );
			}
		}
		echo $output;

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['flickr_id'] = strip_tags( $new_instance['flickr_id'] );
		$instance['flickr_api_key'] = strip_tags( $new_instance['flickr_api_key'] );
		$instance['number'] = $new_instance['number'];
		$instance['thumb_size'] = $new_instance['thumb_size'];
		$instance['refresh_feed'] = $new_instance['refresh_feed'];

		if ( !in_array( $instance['refresh_feed'], array( 5, 60, 1440 ) ) ) {
			$instance['refresh_feed'] = 5;
		}

		delete_transient( 'flickr_widget_photos' . $this->id );

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance, array( 'title' => 'Flickr Photos', 'number' => 5, 'flickr_id' => '', 'flickr_api_key' => '', 'flickr_api_secret' => '' )
		);

		$title = esc_attr( $instance['title'] );
      $flickr_id = esc_attr( $instance['flickr_id'] );
		$flickr_api_key = esc_attr( $instance['flickr_api_key'] );
		$number = absint( $instance['number'] );
		$thumb_size = esc_attr( $instance['thumb_size'] );
		$refresh_feed = absint( $instance['refresh_feed'] );

		// Display the admin form
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'recently-on-flickr-locale' ) ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e( 'Flickr username:', 'recently-on-flickr-locale' ) ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('flickr_id'); ?>" name="<?php echo $this->get_field_name('flickr_id'); ?>" type="text" value="<?php echo $flickr_id; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('flickr_api_key'); ?>"><?php _e( 'Flickr API key:', 'recently-on-flickr-locale' ) ?></label>
			</p>
				<input class="widefat" id="<?php echo $this->get_field_id('flickr_api_key'); ?>" name="<?php echo $this->get_field_name('flickr_api_key'); ?>" type="text" value="<?php echo $flickr_api_key; ?>" />
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of photos to display:', 'recently-on-flickr-locale' ) ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('thumb_size'); ?>"><?php _e( 'Thumbnail size:', 'recently-on-flickr-locale' ) ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('thumb_size'); ?>" name="<?php echo $this->get_field_name('thumb_size'); ?>">
					<?php
						$sizes = array(
							'square' => __( 'Small square (75px)', 'recently-on-flickr-locale' ),
							'square_150' => __('Large square (150px)', 'recently-on-flickr-locale')
						);
						foreach ( $sizes as $size => $display_size ) :
					?>
						<option value="<?php echo $size; ?>"<?php if ( $thumb_size == $size ) echo ' selected="selected"' ?>><?php echo $display_size; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('refresh_feed'); ?>"><?php _e( 'Refresh feed:', 'recently-on-flickr-locale' ) ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('refresh_feed'); ?>" name="<?php echo $this->get_field_name('refresh_feed'); ?>">
					<?php
						$rates = array(
							5 => __( 'Every 5 minutes', 'recently-on-flickr-locale' ),
							60 => __( 'Once per hour', 'recently-on-flickr-locale' ),
							1440 => __( 'Once per day', 'recently-on-flickr-locale' )
						);
						foreach ( $rates as $rate => $display_rate ) :
					?>
						<option value="<?php echo $rate; ?>"<?php if ( $refresh_feed == $rate ) echo ' selected="selected"' ?>><?php echo $display_rate; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		load_plugin_textdomain( 'recently-on-flickr-locale', false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {

		delete_transient( 'flickr_widget_photos' . $this->id );

	} // end deactivate

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( 'recently-on-flickr-widget-styles', plugins_url( 'recently-on-flickr-widget/css/widget.css' ) );

	} // end register_widget_styles

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("RO_Flickr");' ) );
