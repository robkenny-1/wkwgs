<?php
/*
    "WordPress Plugin Template" Copyright (C) 2018 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once(WP_PLUGIN_DIR . '/woocommerce/includes/interfaces/class-wc-logger-interface.php' );
include_once(WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-logger.php');
include_once('PluginCore/Wkwgs_LifeCycle.php');

class Wkwgs_Logger extends WC_Logger
{
    public function log_function( $func )
    {
        $msg = "===== Function ===== $func";
        $this->log( "debug", $msg );
    }
    public function log_var( $var_name, $var )
    {
        $var_text = isset( $var ) ? wc_print_r( $var, true ) : '(unset)';
        $msg = "===== Variable ===== $var_name = " . $var_text;
        $this->log( "debug", $msg );
    }
    public function log_msg( $msg )
    {
        $this->log( "debug", $msg );
    }

	public function log( $level, $message, $context = array() )
    {
        if ( ! isset( $context[ 'source' ]))
        {
            $context[ 'source' ] = 'wkwgs';
        }
        parent::log( $level, $message, $context );
    }

}