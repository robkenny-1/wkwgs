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
include_once('Field.php');

/**
 * The email input class
 *
 * @since 1.0.0
 */
class Email extends Text
{
    const Class_Type        = 'email';
    const Class_Attributes  = array(
        'type'              => self::Class_Type,
    );

    function __construct( $name )
    {
        parent::__construct( $name );
    }

    /**
     * Attributes of a checkbox
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $parent = parent::get_attributes_default();

        return array_merge($parent, self::Class_Attributes);
    }
}