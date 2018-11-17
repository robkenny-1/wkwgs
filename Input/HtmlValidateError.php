<?php
/*
    Input Copyright (C) 2018 Rob Kenny

    Input is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Input is distributed in the hope that it will be useful,
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

include_once('Attributes.php');
include_once(__DIR__ . '/../Wkwgs_Logger.php' );

include_once('Base.php');

/*-------------------------------------------------------------------------*/
/* Interfaces */
/*-------------------------------------------------------------------------*/

interface IHtmlValidateError
{
    public function get_error() : string;
    public function get_object() : ?IHtmlElement;
    public function get_name() : string;
}

class HtmlValidateError implements IHtmlValidateError
{
    protected $error;
    protected $name;
    protected $object;

    public function __construct( string $error, string $name, IHtmlElement $object )
    {
        $this->error  = $error      ?? '';
        $this->name   = $name       ?? '';
        $this->object = $object;
    }
    public function get_error() : string
    {
        return $this->error;
    }
    public function get_object() : ?IHtmlElement
    {
        return $this->object;
    }
    public function get_name() : string
    {
        return $this->name;
    }
}
