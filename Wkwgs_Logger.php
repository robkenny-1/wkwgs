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
    const log_file_name = __DIR__ . '/../../../wkwgs.log';
    
    public static function clear()
    {
        $previous_error_handler = set_error_handler( "\Wkwgs_Logger::ignore_file_not_exist_error" );

        unlink( Wkwgs_Logger::log_file_name );

        set_error_handler( $previous_error_handler );
    }
    public static function log_function( $func )
    {
        $msg = PHP_EOL . "===== Function ===== $func";
        Wkwgs_Logger::log( $msg );
    }
    public static function log_var( $var_name, $var )
    {
        $msg  = "----- Variable ----- $var_name" . PHP_EOL;
        if ( isset( $var ) )
        {
            if ( gettype( $var ) === 'boolean' )
            {
                $msg .= $var ? 'True' : 'False';
            }
            else
            {
                $msg .= print_r( $var, true );
            }
        }
        else
        {
            $msg .= "Type = (unset)" . PHP_EOL;
        }
        Wkwgs_Logger::log( $msg );
    }
    public static function log_msg( $msg )
    {
        $msg = "------ Message ----- $func";
        Wkwgs_Logger::log( $msg );
    }

	public static function log( $message )
    {
        file_put_contents( Wkwgs_Logger::log_file_name, $message . PHP_EOL, FILE_APPEND );
    }

    private static function ignore_file_not_exist_error( int $errno , string $errstr, string $errfile, int $errline, array $errcontext )
    {
        if (strpos($errstr, 'No such file or directory') !== false)
        {
            return True;
        }
        self::log_function( 'ignore_file_not_exist_error');
        self::log_var( '$errno'             , $errno            );
        self::log_var( '$errstr'            , $errstr           );
        self::log_var( '$errfile'           , $errfile          );
        self::log_var( '$errline'           , $errline          );
        self::log_var( '$errcontext'        , $errcontext       );
        self::log_var( 'error_get_last()'   , error_get_last()  );
        return False;
    }
}