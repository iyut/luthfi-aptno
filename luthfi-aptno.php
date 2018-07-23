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
	add_action('woocommerce_checkout_process', 'luthfi_aptno_validate_fields');
}

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

function luthfi_aptno_validate_fields(){
	if ( isset( $_POST['billing_apartmentno'] ) && !is_numeric( $_POST['billing_apartmentno'] ) ){
		wc_add_notice( esc_html__( 'Please enter the apartment number with number.', 'luthfi-aptno' ), 'error' );
	}
}
