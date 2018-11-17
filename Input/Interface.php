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
    public function get_tag() : string;
    public function get_name() : string;
}

interface IHtmlInputElement
{
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