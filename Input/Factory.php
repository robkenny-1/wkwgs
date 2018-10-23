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

namespace Input;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once('Constants.php');
include_once('Form.php');
include_once('Checkbox.php');
include_once('RadioButton.php');
include_once('Text.php');
include_once('Email.php');
include_once('Telephone.php');
include_once('Button.php');
include_once('Label.php');

class Factory
{
    /*-------------------------------------------------------------------------*/
    /* Class factory */
    /*-------------------------------------------------------------------------*/
    private const FactoryMachines = array(
        // Name of input object     // Input object class name
        Form        ::Input_Type        => 'Input\Form',
        Checkbox    ::Input_Type        => 'Input\Checkbox',
        RadioButton ::Input_Type        => 'Input\RadioButton',
        Button      ::Input_Type        => 'Input\Button',
        Text        ::Input_Type        => 'Input\Text',
        Email       ::Input_Type        => 'Input\Email',
        Telephone   ::Input_Type        => 'Input\Telephone',
        Label       ::Input_Type        => 'Input\Label',
    );

    public static function Get( $field_attrs )
    {
        \Wkwgs_Logger::log_function( 'Factory::Get');

        if ( isset( $field_attrs[ 'name' ] ) && isset( $field_attrs[ 'type' ] ) )
        {
            $type = $field_attrs[ 'type' ];
            \Wkwgs_Logger::log_var( '$type', $type );

            if ( isset( Factory::FactoryMachines[ $type ] ) )
            {
                $machine = Factory::FactoryMachines[ $type ];
                $field = new $machine( $field_attrs );
                return $field;
            }
            \Wkwgs_Logger::log_msg( "$type does not exist" );
        }

        return null;
    }
}