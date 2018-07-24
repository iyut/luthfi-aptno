<?php
/*
Plugin Name: Luthfi - Apartment Number
Plugin URI: https://github.com/iyut/luthfi-aptno
Description: Adding apartment number field at checkout page
Version: 0.8.0
Author: Luthfi
Author URI: http://www.interfeis.com
License: GPLv2 or later
WC requires at least: 2.0.0
WC tested up to: 3.3.5
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
add_action( 'plugins_loaded', 'luthfi_aptno_init', 0 );

function luthfi_aptno_init(){

	add_filter('woocommerce_checkout_fields', 'luthfi_aptno_add_fields');
	add_filter('woocommerce_order_formatted_billing_address', 'luthfi_aptno_display_field_ba', 99, 2);
	add_filter('woocommerce_formatted_address_replacements', 'luthfi_aptno_remap_address_field', 99, 2);
	add_filter('woocommerce_localisation_address_formats', 'luthfi_aptno_format_addresses',99);
	add_filter('woocommerce_locate_template', 'luthfi_aptno_woocommerce_locate_template', 10, 3);

	add_action('woocommerce_checkout_process', 'luthfi_aptno_validate_fields');
	add_action('woocommerce_checkout_update_order_meta', 'luthfi_aptno_save_fields', 10, 2);

}

function luthfi_aptno_plugin_path(){

	return untrailingslashit( plugin_dir_path( __FILE__ ) );

}

function luthfi_aptno_add_fields( $fields ){

	$fields['billing']['billing_apartmentno'] = array(
		'type'		=> 'number',
        'label'     => esc_html__('Apartment Number', 'luthfi-aptno'),
    	'placeholder'   => _x('Apartment Number', 'placeholder', 'luthfi-aptno'),
    	'required'  => false,
    	'class'     => array('form-row-wide'),
    	'clear'     => true
     );

     return $fields;

}

function luthfi_aptno_validate_fields(){

	if( isset( $_POST['billing_apartmentno'] ) && !is_numeric( $_POST['billing_apartmentno'] ) ){

		wc_add_notice( '<strong>'. esc_html__( 'Apartment number', 'luthfi-aptno' ) .'</strong>'. ' ' .esc_html__( 'must be a number.', 'luthfi-aptno' ), 'error' );

	}

}

function luthfi_aptno_save_fields( $order_id, $data ){

	if( !empty( $data['billing_apartmentno'] ) ){

		update_post_meta($order_id, 'billing_apartmentno', sanitize_text_field( $data['billing_apartmentno'] ));

	}

}

function luthfi_aptno_show_field( $order_id ){

	$aptno = get_post_meta( $order_id, 'billing_apartmentno');
?>
	<p class="woocommerce-field aptno">
		<b><?php esc_html_e('Apartment No :', 'luthfi-aptno'); ?></b> <?php echo esc_html( $aptno ); ?>
	</p>
<?php

}

function luthfi_aptno_display_field_ba($value, $order){

	$order_id 	= $order->get_id();

	$aptno					= get_post_meta($order_id, 'billing_apartmentno', true);
	$value['apartmentno']	= sanitize_text_field($aptno);

	return $value;

}

function luthfi_aptno_remap_address_field($value, $args){

	$offset = 6;
	$new_value = array_slice($value, 0, $offset, true) +
            	array('{apartmentno}' => esc_html__('Apartment No :', 'luthfi-aptno').' '. esc_html( $args['apartmentno'] )) +
            	array_slice($value, $offset, NULL, true);

	return $new_value;

}

function luthfi_aptno_format_addresses($value){

	$value['default'] = "{name}\n{company}\n{address_1}\n{address_2}\n{apartmentno}\n{city}\n{state}\n{postcode}\n{country}";

	return $value;

}

function luthfi_aptno_woocommerce_locate_template( $template, $template_name, $template_path ){

	global $woocommerce;

	$_template = $template;

	if ( ! $template_path ) $template_path = $woocommerce->template_url;

	$plugin_path  = luthfi_aptno_plugin_path() . '/woocommerce/';

	$template = locate_template(
		array(
			$template_path . $template_name,
			$template_name
		)
	);

	if ( ! $template && file_exists( $plugin_path . $template_name ) )
	$template = $plugin_path . $template_name;


	if ( ! $template )
	$template = $_template;

	// Return what we found
	return $template;

}
