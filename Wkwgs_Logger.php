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

class Wkwgs_Logger 
{
    const log_file_name = __DIR__ . '/../../../wp-content/uploads/wc-logs/wkwgs.log';
    
    public static function clear()
    {
        unlink( Wkwgs_Logger::log_file_name );
    }
    public static function log_function( $func )
    {
        $msg = "===== Function ===== $func";
        Wkwgs_Logger::log( $msg );
    }
    public static function log_var( $var_name, $var )
    {
        $var_text = isset( $var ) ? print_r( $var, true ) : '(unset)';
        $msg = "===== Variable ===== $var_name = " . $var_text;
        Wkwgs_Logger::log( $msg );
    }
    public static function log_msg( $msg )
    {
        Wkwgs_Logger::log( $msg );
    }

	public static function log( $message )
    {
        file_put_contents( Wkwgs_Logger::log_file_name, $message . PHP_EOL, FILE_APPEND );
    }
}