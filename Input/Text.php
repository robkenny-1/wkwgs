<?php
/*
    Input Copyright (C) 2018 Rob Kenny

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

namespace Input\HtmlHelper;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once('Constants.php');
include_once('Field.php');

class Text extends InputElement
{
    const Input_Type            = 'text';
    const Default_Attributes    = [
        'type'      => 'text',
    ];

    public function __construct( $desc )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        if ( gettype( $desc ) !== 'array' )
        {
            $logger->log_var( '$desc is not an array', $desc );
            return;
        }

        $desc[ 'tag' ] = self::Input_Type;
        parent::__construct( $desc );

        $this->get_attributes()->set_attributes_default( self::Default_Attributes );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlForm routines */
    /*-------------------------------------------------------------------------*/

    public function validate( $post )
    {
        $this->validation_errors = [];

        if ( empty( $post ) )
        {
            $this->validation_errors[ $this->get_attributes()->get_name() ] =
            [
                'field'         => $this,
                'error'         => '$post is empty',
            ];
        }
        else
        {
        }

        return empty( $this->validation_errors );
    }

    public function get_value( $post )
    {
        $values = [];

        if ( ! empty( $post ) )
        {
        }

        return $values;
    }
}