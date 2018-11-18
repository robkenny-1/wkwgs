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

include_once('Input.php');

/*-------------------------------------------------------------------------*/
/* Interfaces */
/*-------------------------------------------------------------------------*/

interface IHtmlPrinter
{
    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html() : string;
}

interface IHtmlPrinterList extends \Iterator
{
    /**
     * Add a single IHtmlPrinter
     * @param string|IHtmlPrinter $child IHtmlPrinter to add to the list, string are automatically converted to HtmlText class
     */
    public function add_child( IHtmlPrinter $child );
}

interface IHtmlElement extends IHtmlPrinter, IHtmlPrinterList
{
    public function get_tag()   : string;
}

interface IHtmlInput
{
    /**
     * Get the type of Input
     *
     * @return  string Input type
     */
    public function get_type() : string;

    /**
     * Get the name of the HTML input element,
     * this is the index used to retrieve the data from POST
     *
     * @return string name of the input element
     */
    public function get_name()  : string;

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate( array $post ) : array;

    /**
     * Get this object's data in $post
     *
     * @return array,string | string contents of the input object
     */
    public function get_value( array $post );

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id() : string;

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( string $form_id );
}

/*-------------------------------------------------------------------------*/

/**
 * Attributes are a collection of name/value pairs
 * Attributes used by the HTML generation code are
 * split into two: values used for the HTML element and the others.
 *
 */
interface IAttributes
{
    /**
     * Set the attributes of both types
     *
     * @param $attributes associative array of values, contains both types
     * @return null
     */
    public function set_attributes( array $attributes, array $default = [], array $compound = [] );

    /**
     * Get the attributes that are not in $compound
     *
     * @return array, current attributes
     */
    public function get_attributes() : array;

    /**
     * Get the attributes that are in $compound
     *
     * @return indexed array of the alternate values
     */
    public function get_attributes_compound() : array;

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute( string $name );

    /**
     * Get the value of a single alternate attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute_compound( string $name );

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $name, $value );
}

interface IAttributeProvider
{
    public function define_default_attributes() : array;

    public function define_compound_attributes() : array;
}

?>