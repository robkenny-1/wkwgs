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
include_once(__DIR__ . '/../Wkwgs_Logger.php' );

class Field_Error
{
    private $object         = null;
    private $raw_value   = null;
    private $error          = null;

    public function __construct( $input_object, $error, $field_contents = '' )
    {
        $this->object           = $input_object;
        $this->raw_contents     = $field_contents;
        $this->error            = $error;
    }
    public function get_error_value()
    {
        return $this->$raw_value;
    }
    public function get_error_object()
    {
        return $this->object;
    }
    public function get_error()
    {
        return $this->error;
    }
    public function get_error_object_name()
    {
        return $this->object->get_name();
    }
}