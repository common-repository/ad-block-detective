<?php
/*
 * Plugin Name: Ad Block Detective
 * Plugin URI: http://abdetective.sourceforge.net
 * Description: Detects installed ad blockers on visitors browser and provides several options to handle these users
 * Version: 0.5.3
 * Author: Heiko Irrgang
 * Author URI: http://93-interactive.com
 * License: GPL
 */

/**
 * ABDetective - Ad Block Detective
 * Copyright (c) 2011 Heiko Irrgang
 *
 * This file is part of Ad Block Detective.
 * 
 * Ad Block Detective is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FAd Block Detective is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Ad Block Detective. If not, see <http://www.gnu.org/licenses/>.
 */

load_plugin_textdomain( 'abdetective', false, 'abdetective/languages' );

require_once dirname(__FILE__).'/lib/ABDetective.class.php';

function abdetective_go( $buf ) {
	if ( ( !is_admin() ) && ( ABDetective::getQueryFile() != 'wp-login.php' ) ) {
		ABDetective::setRedirectURL( get_option( 'abdetective_redirect' ) );
		ABDetective::setInfoName( get_option( 'abdetective_info' ) );
		ABDetective::setPluginDir(
			sprintf( '%s/%s',
				WP_PLUGIN_DIR,
				dirname( plugin_basename( __FILE__ ) )
			)
	       	);
		switch ( get_option( 'abdetective_mode' ) ) {
		case 'redirect':
			return ABDetective::modeRedirect( $buf );
		case 'shareware':
			return ABDetective::modeShareware( $buf );
		default:
			return ABDetective::modeBlock( $buf );
		}
	}
	return $buf;
}

function abdetective_init() {
	ob_start( 'abdetective_go' );
}

add_action( 'init', 'abdetective_init' );

function abdetective_admin_setup() {
	add_options_page( 'ABDetective Options', 'ABDetective', 8, 'abdetectiveoptions', 'abdetective_options_page' );
}

add_action('admin_menu', 'abdetective_admin_setup');

function abdetective_options_page() {
	include dirname(__FILE__).'/abdetectiveoptions.php';
}

function abdetective_admin_head() {
	echo( "<script type=\"text/javascript\">
		/* <![CDATA[ */
		(function() {
			var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
			s.type = 'text/javascript';
			s.async = true;
			s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
			t.parentNode.insertBefore(s, t);
		})();
		/* ]]> */
	</script>" );
}

add_action('admin_head', 'abdetective_admin_head');

register_activation_hook( __FILE__, 'abdetective_activate' );

function abdetective_activate() {
	if ( strlen( get_option( 'abdetective_info' ) ) < 1 ) {
		update_option( 'abdetective_info', ABDetective::generateRandom( '' ).".html" );
	}
}
