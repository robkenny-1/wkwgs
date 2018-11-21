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

/*-------------------------------------------------------------------------*/

/*
 * Attributes are a collection of name/value pairs
 */
interface IAttribute
{
    /**
     * Set the attributes of both types
     *
     * @param array $attributes associative array of attribute name/value
     * @return null
     */
    public function set_attributes( array $attributes, array $default = [] );

    /**
     * Get the attributes that are not in $compound
     *
     * @return array, current attributes
     */
    public function get_attributes() : array;

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute( string $name );

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $name, $value );
}

interface IAttributeProvider
{
    public function define_attribute_default() : array;
}

/*-------------------------------------------------------------------------*/

/*
 * Compound attributes are split into two groups
 * The second group is defined by the attributes passed to set_attribute_seconday
 */
interface IAttributeSeconday extends IAttribute
{
    /**
     * Define attributes that belong to compound elements
     *
     * @param array $compound non-associative array of attribute names
     * that exist for the compound elements
     * @return null
     */
    public function set_attribute_seconday( array $compound );

    /**
     * Get the attributes that are in $compound
     *
     * @return indexed array of the alternate values
     */
    public function get_attributes_seconday() : array;

    /**
     * Get the value of a single alternate attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute_secondary( string $name );
}

interface IAttributeSecondayProvider extends IAttributeProvider
{
    public function define_attribute_seconday() : array;
}

/*-------------------------------------------------------------------------*/

interface IHtmlElement extends IHtmlPrinter, IHtmlPrinterList, IAttributeSecondayProvider, IAttributeSeconday
{
    public function get_tag()   : string;
}

/*-------------------------------------------------------------------------*/

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

?>