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
include_once('Checkbox.php');

class Factory
{
    /*-------------------------------------------------------------------------*/
    /* Class factory */
    /*-------------------------------------------------------------------------*/
    private const FactoryMachines = array(
        Checkbox::Class_Type    => 'Input\Checkbox',
    );

    public static function Get( $field_attrs )
    {
        if ( isset( $field_attrs[ 'name' ] ) && isset( $field_attrs[ 'type' ] ) )
        {
            $name = $field_attrs[ 'name' ];
            $type = $field_attrs[ 'type' ];

            $machine = Factory::FactoryMachines[ $type ];
            if ( isset( $machine ) )
            {
                $field = new $machine( $name );
                $field->set_attributes( $field_attrs );
                return $field;
            }
        }

        return null;
    }
}