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

}

function luthfi_aptno_add_field(){

}
