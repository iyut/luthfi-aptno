<?php
/*
Plugin Name: Luthfi - Apartment Number
Plugin URI: https://github.com/iyut/luthfi-aptno
Description: Adding apartment number field at checkout page
Version: 1.0.0
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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Make sure we don't expose any info if called directly
add_action( 'plugins_loaded', 'luthfi_aptno_init', 0 );

/**
 * Main init of the plugin.
 *
 * Initiate all the hooks
 *
 * @since  1.0.0
 * @return void
 */
function luthfi_aptno_init(){

	add_filter('woocommerce_checkout_fields', 'luthfi_aptno_add_fields');
	add_filter('woocommerce_order_formatted_billing_address', 'luthfi_aptno_display_field_ba', 99, 2);
	add_filter('woocommerce_formatted_address_replacements', 'luthfi_aptno_remap_address_field', 99, 2);
	add_filter('woocommerce_localisation_address_formats', 'luthfi_aptno_format_addresses',99);
	add_filter('woocommerce_locate_template', 'luthfi_aptno_woocommerce_locate_template', 10, 3);

	add_action('woocommerce_checkout_process', 'luthfi_aptno_validate_fields');
	add_action('woocommerce_checkout_update_order_meta', 'luthfi_aptno_save_fields', 10, 2);

}

/**
 * Return the plugin directory path
 *
 * @since  1.0.0
 * @return string
 */
function luthfi_aptno_plugin_path(){

	return untrailingslashit( plugin_dir_path( __FILE__ ) );

}

/**
 * Add a new field for apartment number
 *
 * @since  1.0.0
 * @param  array $checkout fields
 * @return array
 */
function luthfi_aptno_add_fields( $fields ){

	$fields['billing']['billing_apartmentno'] = array(
		'type'		=> 'text',
        'label'     => esc_html__('Apartment Number', 'luthfi-aptno'),
    	'placeholder'   => _x('Apartment Number', 'placeholder', 'luthfi-aptno'),
    	'required'  => false,
    	'class'     => array('form-row-wide'),
    	'clear'     => true
     );

     return $fields;

}

/**
 * Validating the apartment number field.
 *
 * @since  1.0.0
 * @return void
 */
function luthfi_aptno_validate_fields(){

	if( isset( $_POST['billing_apartmentno'] ) && !empty( $_POST['billing_apartmentno'] ) && !is_numeric( $_POST['billing_apartmentno'] ) ){

		wc_add_notice( '<strong>'. esc_html__( 'Apartment number', 'luthfi-aptno' ) .'</strong>'. ' ' .esc_html__( 'must be a number.', 'luthfi-aptno' ), 'error' );

	}

}

/**
 * Saving the apartment number field into order meta data.
 *
 * @since  1.0.0
 * @param  int $order_id, Array $data
 * @return void
 */
function luthfi_aptno_save_fields( $order_id, $data ){

	if( !empty( $data['billing_apartmentno'] ) ){

		update_post_meta($order_id, 'billing_apartmentno', sanitize_text_field( $data['billing_apartmentno'] ));

	}

}

/**
 * Adding an apartment number data into billing address
 *
 * @since  1.0.0
 * @param  Array $billing address value and Object $order.
 * @return Array
 */
function luthfi_aptno_display_field_ba($value, $order){

	$order_id 	= $order->get_id();

	$aptno					= get_post_meta($order_id, 'billing_apartmentno', true);
	$value['apartmentno']	= sanitize_text_field($aptno);

	return $value;

}

/**
 * Adding an apartment number data into billing address and remapping the address field
 *
 * @since  1.0.0
 * @param  array $address map, array $checkout args
 * @return Array
 */
function luthfi_aptno_remap_address_field($value, $args){

		$apt_val = !empty( $args['apartmentno'] )? esc_html__('Apartment No :', 'luthfi-aptno').' '. esc_html( $args['apartmentno'] ) : "";

		$offset = 6;
		$new_value = array_slice($value, 0, $offset, true) +
	            	array('{apartmentno}' => $apt_val ) +
	            	array_slice($value, $offset, NULL, true);

		$value = $new_value;



	return $value;

}

/**
 * Changing the default format addresses by adding apartment number
 *
 * @since  1.0.0
 * @param  array $format address
 * @return Array
 */
function luthfi_aptno_format_addresses($value){

	$value['default'] = "{name}\n{company}\n{address_1}\n{address_2}\n{apartmentno}\n{city}\n{state}\n{postcode}\n{country}";

	return $value;

}

/**
 * Changing the default woocommerce locate template
 *
 * @since  1.0.0
 * @return Array
 */
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
