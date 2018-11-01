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
    static $Indent = 0;

    public static function indent()
    {
        return str_repeat( ' ', self::$Indent );
    }
    public static function ends_with_eol( $text )
    {
        return substr( $text, -1 ) === PHP_EOL;
    }
    
    public static function clear()
    {
        $previous_error_handler = set_error_handler( "self::ignore_file_not_exist_error" );

        unlink( self::log_file_name );

        set_error_handler( $previous_error_handler );
    }
    public static function log_function( $func, $params = null )
    {
        $message = "===== Enter $func";
        self::log_internal( $message );

        if ( ! empty( $params ) )
        {
            $i = 0;
            foreach ( $params as $param )
            {
                $i = $i + 1;
                self::log_param( $i, $param );
            }
        }
    }
    public static function log_return( $func, $value = null )
    {
        if ( isset( $value ) )
        {
            self::log_var( 'return', $value );
        }
        $message = "===== Exit  $func";
        self::log_internal( $message );
    }
    public static function log_value( $var_name, $var, $prefix = 'Variable' )
    {
        if ( isset( $var ) )
        {
            if ( gettype( $var ) === 'boolean' )
            {
                $var = $var ? 'True' : 'False';
            }
            else
            {
                $var = print_r( $var, true );
            }
        }
        else
        {
            $var = "(unset)";
        }
        $message   = "----- $prefix $var_name = $var" . PHP_EOL;
        self::log_internal( $message );
    }
    public static function log_var( $var_name, $var )
    {
        self::log_value( $var_name, $var, 'Variable' );
    }
    public static function log_param( $var_name, $var )
    {
        self::log_value( $var_name, $var, 'Param' );
    }
    public static function log_msg( $message )
    {
        $message = "------ Message ----- $message";
        self::log_internal( $message );
    }
	public static function log( $message )
    {
        self::log_internal( $message );
    }
	protected static function log_internal( $message )
    {
        if ( is_null( $message ) )
        {
            return;
        }

        $full_msg = '';
        foreach( explode( PHP_EOL, $message ) as $text )
        {
            if ( ! empty( $text ) )
            {
                $full_msg .= self::indent() . $text . PHP_EOL;
            }
        }
        self::save_text_to_file( $full_msg );
    }

	protected static function save_text_to_file( $text )
    {
        if ( ! empty( $text ) )
        {
            file_put_contents( self::log_file_name, $text, FILE_APPEND );
        }
    }

    protected static function ignore_file_not_exist_error( int $errno , string $errstr, string $errfile, int $errline, array $errcontext )
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

class Wkwgs_Function_Logger extends Wkwgs_Logger
{
    private $function_name = '';

    public function __construct( $func, $params = null )
    {
        $this->function_name = $func;
        Wkwgs_Logger::$Indent += 2;
        $this->log_function( $this->function_name, $params );
    }

    public function __destruct()
    {
        $this->log_return( $this->function_name );
        Wkwgs_Logger::$Indent -= 2;
    }

    public static function log_var( $var_name, $var )
    {
        Wkwgs_Logger::log_var( $var_name, $var );
    }
    public static function log_msg( $message )
    {
        Wkwgs_Logger::log_msg( $message );
    }
	public static function log( $message )
    {
        Wkwgs_Logger::log( $message );
    }
}