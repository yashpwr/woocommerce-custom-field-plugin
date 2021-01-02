<?php

/**
 * Plugin Name: WooComerce custom fields
 * Author: Yash Pawar
 * Version: 1.0
 */

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');

// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');

//display below price
add_filter( 'woocommerce_get_price_html', 'bd_rrp_sale_price_html', 100, 2 );

add_action( 'wp_head','custom_css' );  

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

    // Sponsor
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

function woocommerce_product_custom_fields_save($post_id){
    // Custom Product Text Field
    $_date = $_POST['_date'];
    $_sponsor = $_POST['_sponsor'];
    $_enable_registeration_your_intrest = $_POST['_enable_registeration_your_intrest'];

    update_post_meta($post_id, '_enable_registeration_your_intrest', esc_attr($_enable_registeration_your_intrest));

    //if (!empty($_date)){
    update_post_meta($post_id, '_date', esc_attr($_date));
    //}

    //if (!empty($_sponsor)){
    update_post_meta($post_id, '_sponsor', esc_attr($_sponsor));
    //}

}

function bd_rrp_sale_price_html( $price, $product ) {

    $return_string =  $price;

    $price2 = $price;

    global $product;

    // Get the custom field value
    $_date = get_post_meta( $product->get_id(), '_date', true );
    $_sponsor = get_post_meta( $product->get_id(), '_sponsor', true );

    if (!is_admin()) {

    // Display
        if ( is_product() ) {

            if( ! empty($_date) ){
            //$return_string .= '<p class="my-custom-field">'.$_date.'</p>';
                $return_string .= '<h2 class="woocommerce-loop-product__title event_date"><strong>'.$_date.'</strong></h2>';
            }

            if( ! empty($_sponsor) ){
            //$return_string .= '<p class="my-custom-field">'.$_sponsor.'</p>';
                $return_string .= '<h2 class="woocommerce-loop-product__title custom_sponsor">Sponsored by: <strong>'.$_sponsor.'</strong></h2>';
            }

        }else {

            $return_string = "";

            if( ! empty($_date) ){
                $return_string .= '<h2 class="woocommerce-loop-product__title event_date">'.$_date.'</h2>';
            }

            if( ! empty($_sponsor) ){
                $return_string .= '<h2 class="woocommerce-loop-product__title custom_sponsor">Sponsored by: '.$_sponsor.'</h2>';
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


function custom_css() { ?>

    <style>
        h2.woocommerce-loop-product__title {
            padding: 0px !important;
            padding-top: 3px !important;
            /*text-align: left !important;*/
        }

        h2.woocommerce-loop-product__title.custom_title {
            padding-bottom: 5px !important;
        }

        p.price {
            padding-bottom: 2px !important;
            /*text-align: left !important;*/
        }

        p.my-custom-field {
            padding-bottom: 2px !important;
        }
        h2.woocommerce-loop-product__title.custom_sponsor {
            margin-bottom: 15px !important;
        }
    </style>

<?php }
