<?php
/**
* Plugin Name: Your Simple Slider
* Description: Its very simple plugin for image slider
* Version: 2.0.4
* Author: Vladyslav Lykhenko
* Author URI: //lihenko.com.ua
* Text Domain: your-simple-slider
* Domain Path: /languages
**/

	/**
	* Register the "book" custom post type
	*/
	function your_simple_slider_setup_post_type() {

		add_action( 'admin_enqueue_scripts', 'add_media_script' );

		function add_media_script( $hook_suffix ) {

		  wp_enqueue_media();

		}

		register_post_type( 'your_simple_slider', 
			array(
				'labels'      => array(
					'name'          => __( 'Your Simple Slider', 'your-simple-slider' ),
					'singular_name' => __( 'Simple Slider', 'your-simple-slider' ),
				),
				'public'      => true,
				'has_archive' => true,
				'supports'    => array( 'title'),
				'rewrite'     => array( 'slug' => 'slider' ), 
				'menu_icon'   => 'dashicons-format-gallery',
			)
		 );

		add_action( 'add_meta_boxes', 'your_simple_slider_meta_box_add', 10, 2 );
 
		function your_simple_slider_meta_box_add( $post_type, $post ) {
		    add_meta_box('your_simple_slider_feat_img_slider', // meta box ID
		        __('Featured Image Gallery', 'your-simple-slider'), // meta box title
		        'your_simple_slider_print_box', // callback function that prints the meta box HTML
		        'your_simple_slider', // post type where to add it
		        'normal', // priority
		        'default' ); // position
		    add_meta_box('your_simple_slider_shortcode', // meta box ID
		        __('Shortcode', 'your-simple-slider'), // meta box title
		        'your_simple_slider_shorcode_box', // callback function that prints the meta box HTML
		        'your_simple_slider', // post type where to add it
		        'side', // priority
		        'default' ); // position
		}
		 
		function your_simple_slider_image_uploader_field( $name, $value = '' ) {

			global $post;
		     
		    $image = 'Upload Image';
		    $button = 'button';
		    $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
		    $display = 'none'; // display state of the "Remove image" button

		    $height = get_post_meta($post->ID, 'slider_height', true);
		    $heightunits = get_post_meta($post->ID, 'height_units', true);
		    $speed = get_post_meta($post->ID, 'slider_speed', true);
		    $arrow = get_post_meta($post->ID, 'slider_arrow', true);
		    $bullet = get_post_meta($post->ID, 'slider_bullet', true);

	     
		    ?>
		    <p>
		    	<label for="slider-height"><?php _e('Slider Height',  'your-simple-slider'); ?></label>
		    	<input name="slider_height"type="number" id="slider-height" value="<?php if ($height > 0) echo esc_attr($height); else echo '250'; ?>">
		    	<select name="height_units" id="height-units">
    		    	<option value="px" <?php if ($heightunits == "px") echo 'selected'; ?>>px</option>
    		    	<option value="%" <?php if ($heightunits == "%") echo 'selected'; ?>>%</option>
    		    </select>
		    </p>
		    <p>
		    	<label for="slider-arrow"><?php _e('Arrows',  'your-simple-slider'); ?></label>
    		    <select name="slider_arrow" id="slider-arrow">
    		    	<option value="1" <?php if ($arrow == 1) echo 'selected'; ?>><?php _e('Yes',  'your-simple-slider'); ?></option>
    		    	<option value="2" <?php if ($arrow == 2) echo 'selected'; ?>><?php _e('No',  'your-simple-slider'); ?></option>
    		    </select>
	    	</p>
		    <p>
		    	<label for="slider-bullet"><?php _e('Bullets',  'your-simple-slider'); ?></label>
    		    <select name="slider_bullet" id="slider-bullet">
    		    	<option value="1" <?php if ($bullet == 1) echo 'selected'; ?>><?php _e('Yes',  'your-simple-slider'); ?></option>
    		    	<option value="2" <?php if ($bullet == 2) echo 'selected'; ?>><?php _e('No',  'your-simple-slider'); ?></option>
    		    </select>
	    	</p>
	    	<p>
		    	<label for="slider-speed"><?php _e('Slider Speed',  'your-simple-slider'); ?></label>
		    	<input name="slider_speed" type="number" id="slider-speed" value="<?php if ($speed > 0) echo esc_attr($speed); else echo '4000'; ?>">
		    </p> 
		    <p><?php
		        _e( '<i>Set Images for Featured Image Gallery</i>', 'your-simple-slider' );
		    ?></p>
		     
		    <label>
		        <div class="gallery-screenshot clearfix">
		            <?php
		            {
		                $ids = explode(',', $value);
		                foreach ($ids as $attachment_id) {
		                    $img = wp_get_attachment_image_src($attachment_id, 'thumbnail');
		                    echo '<div class="screen-thumb"><img src="' . esc_url($img[0]) . '" /></div>';
		                }
		            }
		            ?>
		        </div>
		         
		        <input id="edit-gallery" class="button upload_gallery_button" type="button"
		               value="<?php esc_html_e('Add/Edit Gallery', 'your-simple-slider') ?>"/>
		        <input id="clear-gallery" class="button upload_gallery_button" type="button"
		               value="<?php esc_html_e('Clear', 'your-simple-slider') ?>"/>
		        <input type="hidden" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" class="gallery_values" value="<?php echo esc_attr($value); ?>">
		    </label>
		<?php   
		}
		 
		/*
		 * Meta Box HTML
		 */
		function your_simple_slider_print_box( $post ) {
		     
		    wp_nonce_field( 'save_feat_gallery', 'your_simple_slider_feat_gallery_nonce' );
		     
		    $meta_key = 'your_simple_slider_img';
		    echo your_simple_slider_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
		}

		function your_simple_slider_shorcode_box( $post ) {

			$shortcode = '[your_simple_slider id=&quot;' . $post->ID . '&quot;]';


			echo '<div id="slider-shortcode">';

			echo  esc_attr($shortcode);

			echo '</div>';
		    
		}
		 
		/*
		 * Save Meta Box data
		 */
		add_action('save_post', 'your_simple_slider_img_gallery_save');
		 
		function your_simple_slider_img_gallery_save( $post_id ) {


			if(isset($_POST["slider_height"])){
				$height = intval( $_POST["slider_height"] );
				if ( ! $height ) {
				  $height = 150;
				}
		    	update_post_meta($post_id, 'slider_height', $height);
		    }
		    if(isset($_POST["height_units"])){
				$heightunits = esc_attr( $_POST["height_units"] );
		    	update_post_meta($post_id, 'height_units', $heightunits);
		    }

		    if(isset($_POST["slider_speed"])){
		    	$speed = intval( $_POST["slider_speed"] );
				if ( ! $speed ) {
				  $speed = 4000;
				}
		    	update_post_meta($post_id, 'slider_speed', $speed);
		    }

		    if(isset($_POST["slider_arrow"])){
		    	$arrow = intval( $_POST["slider_arrow"] );
				if ( ! $arrow ) {
				  $arrow = 1;
				}

				if ( $arrow < 1 || $arrow > 2) {
					$arrow = 1;
				}

		    	update_post_meta($post_id, 'slider_arrow', $arrow);
		    }

		    if(isset($_POST["slider_bullet"])){
		    	$bullet = intval( $_POST["slider_bullet"] );
				if ( ! $bullet ) {
				  $bullet = 1;
				}

				if ( $bullet < 1 || $bullet > 2) {
					$bullet = 1;
				}
		    	update_post_meta($post_id, 'slider_bullet', $bullet);
		    }
		     
		    if ( !isset( $_POST['your_simple_slider_feat_gallery_nonce'] ) ) {
		        return $post_id;
		    }
		     
		    if ( !wp_verify_nonce( $_POST['your_simple_slider_feat_gallery_nonce'], 'save_feat_gallery') ) {
		        return $post_id;
		    } 
		     
		    if ( isset( $_POST[ 'your_simple_slider_img' ] ) ) {
		    	$gallery = $_POST['your_simple_slider_img'];
		    	if (preg_match("/^(?:\d\,?)+\d$/", $gallery)) {
					update_post_meta( $post_id, 'your_simple_slider_img', $gallery );
				} else {
					update_post_meta( $post_id, 'your_simple_slider_img', '' );
				}
		        
		    } else {
		        update_post_meta( $post_id, 'your_simple_slider_img', '' );
		    }
		    
         
		     
		}



	} 
	add_action( 'init', 'your_simple_slider_setup_post_type' );

	function your_simple_slider_register_styles() {

		wp_enqueue_style( 'sclick_slider_style', plugin_dir_url( __FILE__ ) . 'js/slick/slick.css' );

		wp_enqueue_style( 'sclick_slider_theme_style', plugin_dir_url( __FILE__ ) . 'js/slick/slick-theme.css' );

		wp_enqueue_style( 'your_simple_slider_style', plugin_dir_url( __FILE__ ) . 'css/your-simple-slider.css' );

		wp_enqueue_script( 'sclick_slider_script', plugin_dir_url( __FILE__ ) . 'js/slick/slick.min.js', array('jquery'), true );

		wp_enqueue_script( 'resize_sensor_script', plugin_dir_url( __FILE__ ) . 'js/resize-sensor.js', array('jquery'), true );

		wp_enqueue_script( 'your_simple_slider_script', plugin_dir_url( __FILE__ ) . 'js/your-simple-slider.js', array('jquery'), true );

	}

	add_action( 'wp_enqueue_scripts', 'your_simple_slider_register_styles' );


	function your_simple_slider_admin_scripts() {

		wp_enqueue_style( 'your_simple_slider_admin_style', plugin_dir_url( __FILE__ ) . 'css/your-simple-slider-admin.css' );
     
	    wp_enqueue_script( 'your_simple_slider_admin_script', plugin_dir_url( __FILE__ ) . 'js/your-simple-slider-admin.js', array('jquery'), true );
	     
	}
	add_action( 'admin_enqueue_scripts','your_simple_slider_admin_scripts' );


	function your_simple_slider_shortcode($attr){

	    if(!$attr['id']){
	    	return;
	    }

	    $height = get_post_meta( $attr['id'], 'slider_height', true );
	    $heightunits = get_post_meta( $attr['id'], 'height_units', true );
	    $speed = get_post_meta( $attr['id'], 'slider_speed', true );

	    if (get_post_meta( $attr['id'], 'slider_arrow', true ) == 1){
	    	$arrow = 'true';
	    } else {
	    	$arrow = 'false';
	    }

	    if (get_post_meta( $attr['id'], 'slider_bullet', true ) == 1){
	    	$bullet = 'true';
	    } else {
	    	$bullet = 'false';
	    }

	 
	    $output = '<div id="flexslider-' . esc_attr($attr['id']) . '" class="flexslider" data-height-units="' . esc_attr($heightunits) . '" data-slider-height="' . esc_attr($height) . '">';
  		$output .= '<div class="slides">';
  			$image_ids = get_post_meta( $attr['id'], 'your_simple_slider_img' );
         	
		    if ( ! empty( $image_ids ) ) :
		        $image_ids = explode( ',', $image_ids[0] );
		        foreach($image_ids as $image_id) {
		            $image_url = wp_get_attachment_url($image_id);
		            if($heightunits == 'px'){
		            	$output .= '<div class="slider-item"><div style="background-image:url(' . esc_url($image_url) .'); height:' . esc_attr($height) .'px;"></div></div>';	
		            } else {
		            	$output .= '<div class="slider-item"><div style="background-image:url(' . esc_url($image_url) .');"></div></div>';
		            }
		            
		        }
		    endif;
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<script>';
		$output .= 'jQuery(document).ready(function(jQuery) {';
		$output .= 'jQuery("#flexslider-' . esc_attr($attr['id']) .' .slides").slick({';
    	$output .= 'autoplay: true,';
    	$output .= 'animation: "slide",';
    	$output .= 'autoplaySpeed: '. esc_attr($speed) . ',';
    	$output .= 'speed: 300,';
    	$output .= 'slidesToShow: 1,';
    	$output .= 'slidesToScroll: 1,';
    	$output .= 'dots: '. esc_attr($bullet) .',';
    	$output .= 'arrows: '. esc_attr($arrow) .',';
		$output .= '});';
		$output .= '});';
		$output .= '</script>';
	    return $output;
	}
	 

	add_shortcode( 'your_simple_slider' , 'your_simple_slider_shortcode' );


	/**
	* Activate the plugin.
	*/
	function your_simple_slider_activate() { 
		// Trigger our function that registers the custom post type plugin.
		your_simple_slider_setup_post_type(); 

		// Clear the permalinks after the post type has been registered.
		flush_rewrite_rules(); 
	}
	register_activation_hook( __FILE__, 'your_simple_slider_activate' );

	function your_simple_slider_init() {
	    $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages'; 
	    load_plugin_textdomain( 'your-simple-slider', false, $plugin_rel_path );
	}
	add_action('plugins_loaded', 'your_simple_slider_init');


?>