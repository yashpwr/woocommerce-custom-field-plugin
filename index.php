<?php

/**
 * Plugin Name: WooComerce custom fields
 * Author: Yash Pawar
 * Version: 1.0
 */


// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');

function woocommerce_product_custom_fields(){
	global $woocommerce, $post;

	echo '<div class="product_custom_field">';

    // Date
	woocommerce_wp_text_input(
		array(
			'id' => '_date',
			'placeholder' => ' Enter Date',
			'label' => __('Date', 'woocommerce'),
			'desc_tip' => 'true'
		)
	);

    // Sponsor
	woocommerce_wp_text_input(
		array(
			'id' => '_sponsor',
			'placeholder' => ' Enter Sponsor',
			'label' => __('Sponsor', 'woocommerce'),
			'desc_tip' => 'true'
		)
	);

	// Sponsor URL
	woocommerce_wp_text_input(
		array(
			'id' => '_sponsor_url',
			'placeholder' => ' Enter Sponsor URL',
			'label' => __('Sponsor URL', 'woocommerce'),
			'desc_tip' => 'true'
		)
	);

    // Register your interest
	woocommerce_wp_text_input(
		array(
			'id' => '_enable_registeration_your_intrest',
			'placeholder' => 'Enter Register your Intrest URL',
			'label' => __('Add Register Your Interest URL', 'woocommerce'),
			'desc_tip' => 'true'
		)
	);

	echo '</div>';

}

// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');

function woocommerce_product_custom_fields_save($post_id){

	$_date = $_POST['_date'];

	$_sponsor = $_POST['_sponsor'];

	$_sponsor_url = $_POST['_sponsor_url'];

	$_enable_registeration_your_intrest = $_POST['_enable_registeration_your_intrest'];

	update_post_meta($post_id, '_enable_registeration_your_intrest', esc_attr($_enable_registeration_your_intrest));

	update_post_meta($post_id, '_sponsor_url', esc_attr($_sponsor_url));

	update_post_meta($post_id, '_date', esc_attr($_date));

	update_post_meta($post_id, '_sponsor', esc_attr($_sponsor));

}



add_filter('woocommerce_short_description','cmk_additional_button', 10, 2);
function cmk_additional_button($content) {
	
	global $product;

	if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
	}

	//$product_ID = $product->get_id();

	//print_r($product); die();

    $_enable_registeration_your_intrest = get_post_meta($product->get_id(), '_enable_registeration_your_intrest', true );

    if( ! $product->managing_stock() && ! $product->is_in_stock() ) {
	    if (!empty($_enable_registeration_your_intrest)) {
	    	$content .= '<br><a href="'.$_enable_registeration_your_intrest.'" target="_blank">
	    	<button type="button" class="single_register_your_interest_button button alt">Register Your Interest</button></a>';
	    }
	}
    
    return $content;
}



//display below price
add_filter( 'woocommerce_get_price_html', 'bd_rrp_sale_price_html', 100, 2 );

function bd_rrp_sale_price_html( $price, $product ) {

	$return_string =  $price;

	$price2 = $price;

	global $product;

    // Get the custom field value
	$_date = get_post_meta( $product->get_id(), '_date', true );
	$_sponsor = get_post_meta( $product->get_id(), '_sponsor', true );
	$_sponsor_url = get_post_meta( $product->get_id(), '_sponsor_url', true );

	if (!is_admin()) {

    // Display
		if ( is_product() ) {

			if( ! empty($_date) ){
            //$return_string .= '<p class="my-custom-field">'.$_date.'</p>';
				$return_string .= '<h2 class="woocommerce-loop-product__title event_date"><strong>'.$_date.'</strong></h2>';
			}

			if( ! empty($_sponsor) ){
            //$return_string .= '<p class="my-custom-field">'.$_sponsor.'</p>';
				$return_string .= '<h3 class="woocommerce-loop-product__title custom_sponsor">SPONSOR : ';
				
				if (! empty($_sponsor_url)) {
					$return_string .= '<strong><a href="'.$_sponsor_url.'" class="sponsor_url_a" target="_blank">'.$_sponsor.'</a></strong>';
				}else{
					$return_string .= '<strong>'.$_sponsor.'</strong>';
				}

				$return_string .= "</h3>";
				
			}

		}else {

			$return_string = "";

			if( ! empty($_date) ){
				$return_string .= '<h2 class="woocommerce-loop-product__title event_date">'.$_date.'</h2>';
			}

			if( ! empty($_sponsor) ){
				$return_string .= '<h2 class="woocommerce-loop-product__title custom_sponsor">SPONSOR : '.$_sponsor.'</h2>';
			}

			$return_string .= $price;

		}

	}

	return $return_string;

}

add_action('admin_head', 'my_column_width');

function my_column_width() {
	echo '<style type="text/css">';
	echo 'table.wp-list-table .column-event_date { width: 100px; text-align: left!important;padding: 5px;}';
	echo 'table.wp-list-table .column-sponsor { width: 100px; text-align: left!important;padding: 5px;}';
	echo '</style>';
}


// Add product new column in administration
add_filter( 'manage_edit-product_columns', 'woo_product_weight_column', 20 );
function woo_product_weight_column( $columns ) {

	$columns['event_date'] = esc_html__( 'Event Date', 'woocommerce' );
	$columns['sponsor'] = esc_html__( 'Sponsor', 'woocommerce' );
	return $columns;

}
// Populate weight column
add_action( 'manage_product_posts_custom_column', 'woo_product_weight_column_data', 2 );
function woo_product_weight_column_data( $column ) {
	global $post;

	$_date = get_post_meta( $post->ID, '_date', true );
	$_sponsor = get_post_meta( $post->ID, '_sponsor', true );

	if ( $column == 'event_date' ) {

		if (!empty($_date)){
			print $_date;
		}else{
			print 'N/A';
		}
	}

	if ( $column == 'sponsor' ) {
		if (!empty($_sponsor)){
			print $_sponsor;
		}else{
			print 'N/A';
		}
	}


}

add_action( 'wp_head','custom_css' );  

function custom_css() { ?>

	<style>
		h2.woocommerce-loop-product__title {
			padding: 0px !important;
			padding-top: 3px !important;
		}
		h2.woocommerce-loop-product__title.custom_title {
			padding-bottom: 5px !important;
		}
		p.price {
			padding-bottom: 2px !important;
		}
		h2.woocommerce-loop-product__title.event_date {
		    margin-bottom: 5px !important;
		}
		p.my-custom-field {
			padding-bottom: 2px !important;
		}
		h2.woocommerce-loop-product__title.custom_sponsor {
			margin-bottom: 15px !important;
		}
		button.single_register_your_interest_button.button.alt {
		    margin: 15px 0 30px !important;
		}
		a.sponsor_url_a {
		    color: #4c7d3b !important;
		}
	</style>

<?php } ?>
