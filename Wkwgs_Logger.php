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
    public    static $Log_File_Name    = __DIR__ . '/../../../wkwgs.log';
    public    static $Disable          = True;

    protected static $Indent           = 0;

    public static function indent()
    {
        return str_repeat( ' ', self::$Indent );
    }
    public static function ends_with_eol( string $text )
    {
        return substr( $text, -1 ) === PHP_EOL;
    }

    public static function clear()
    {
        $previous_error_handler = set_error_handler( "self::ignore_file_not_exist_error" );

        unlink( Wkwgs_Logger::$Log_File_Name );

        set_error_handler( $previous_error_handler );
    }
    protected static function var_to_text( $var )
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
        return $var;
    }
    public static function log_value( string $var_name, $var, string $prefix = 'Variable' )
    {
        $var = self::var_to_text( $var );
        $message   = "----- $prefix $var_name = $var";
        self::log_internal( $message );
    }
    public static function log_var( string $var_name, $var )
    {
        //self::log_value( $var_name, $var, 'Variable' );
        self::log_value( $var_name, $var, '' );
    }
    public static function log_param( string $var_name, $var )
    {
        self::log_value( $var_name, $var, 'Param' );
    }
    public static function log_msg( string $message )
    {
        $message = "------ $message";
        self::log_internal( $message );
    }
	public static function log( string $message )
    {
        self::log_internal( $message );
    }
	protected static function log_internal( string $message )
    {
        if ( is_null( $message ) || Wkwgs_Logger::$Disable == True )
        {
            return;
        }

        // Normalize EOL
        $message = str_replace( "\r\n", PHP_EOL, $message );
        $message = str_replace( "\r", PHP_EOL, $message );
        $message = str_replace( "\n", PHP_EOL, $message );

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
            file_put_contents( Wkwgs_Logger::$Log_File_Name, $text, FILE_APPEND );
        }
    }

    protected static function ignore_file_not_exist_error( int $errno , string $errstr, string $errfile, int $errline, array $errcontext )
    {
        if (strpos($errstr, 'No such file or directory') !== false)
        {
            return True;
        }
        self::log_msg( '***** ignore_file_not_exist_error');
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
    private $function_return;

    public function __construct( string $func, $params = null, string $class = '' )
    {
        Wkwgs_Logger::$Indent += 2;
        
        $this->function_name = empty( $class ) ? $func : "$class::$func";

        $this->log_function( $params, $class );
    }

    public function __destruct()
    {
        $this->log_return( $this->function_name, $this->function_return );
        Wkwgs_Logger::$Indent -= 2;
    }
        
    protected function get_param_names( string $class = '' ) : array
    {
        $paramNames = [];

        try
        {
            if ( ! empty( $class ) )
            {
                $reflection =  new \ReflectionMethod($class, $this->function_name);
            }
            else
            {
                $reflection =  new \ReflectionFunction($this->function_name);
            }

            $params = $reflection->getParameters();
            $paramNames = array_map(
                function( $item )
                {
                    return $item->getName();
                },
                $params
            );
        }
        catch (Exception $e)
        {
        }

        return $paramNames;
    }

    public function log_function( $params = null, string $class = '' )
    {
        $message = '===== Enter ' . ( empty( $class ) ? $this->function_name : "$class::$this->function_name" );
        self::log_internal( $message );

        if ( ! empty( $params ) )
        {
            $param_names = self::get_param_names( $this->function_name, $class );

            $i = 0;
            foreach ( $params as $param )
            {
                $param_name = $param_names[ $i ] ?? $i;

                $i = $i + 1;
                self::log_param( $param_name, $param );
            }
        }
    }

    public function log_return( $value = null )
    {
        if ( isset( $value ) )
        {
            $value = self::var_to_text( $value );
            $message = '===== Exit ' . $this->function_name . ' = $value';
        }
        else
        {
            $message = '===== Exit ' . $this->function_name;
        }
        self::log_internal( $message );
    }

}