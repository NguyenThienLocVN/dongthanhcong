<?php
/**
 * Shop breadcrumb
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !empty($breadcrumb) ) {

	do_action('flatsome_before_breadcrumb');

	echo $wrap_before;
	foreach ( $breadcrumb as $key => $crumb ) {

		echo $before;

		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		} else if(!is_product() && !flatsome_option('wc_category_page_title')) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		}

		echo $after;
        $tam=1;
		// Single product or Active title
		if(is_product() || flatsome_option('wc_category_page_title')){
				$key = $key+1;
				
				if ( sizeof( $breadcrumb ) > $key+1) {
					echo ' <span class="divider">'.$delimiter.'</span>';
				}
		} else{
		// Category pages
		if ( sizeof( $breadcrumb ) !== $key + 1 ) {
				echo ' <span class="divider">'.$delimiter.'</span>';
			}
		}

	}
	if(is_product()) {
	         $product = wc_get_product( get_the_id() );
	        echo ' <span class="divider">/</span><a href="'.get_permalink($product->ID).'">'.get_the_title($product->ID).'</a>';
	     //  echo do_action( 'woocommerce_shop_loop_item_title');
	}
	
	echo $wrap_after;

	do_action('flatsome_after_breadcrumb');

}