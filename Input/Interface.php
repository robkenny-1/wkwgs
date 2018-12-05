<?php

/*
 * Input Copyright (C) 2018 Rob Kenny
 *
 * WordPress Plugin Template is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WordPress Plugin Template is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Contact Form to Database Extension.
 * If not, see http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Input;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once ('Input.php');

/* ------------------------------------------------------------------------- */
/* Interfaces */
/* ------------------------------------------------------------------------- */
interface IHtmlPrinter
{

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html(): string;
}

interface IHtmlPrinterList extends \Traversable
{

    /**
     * Replace all children with the new values
     *
     * @return null
     */
    public function set_children(?array $children): void;

    /**
     * Add a single IHtmlPrinter
     *
     * @param IHtmlPrinter $child
     *            object to add to the end of the list of children
     */
    public function add_child(IHtmlPrinter $child): void;

    /**
     * Get a RecursiveIteratorIterator
     *
     * @param
     *            RecursiveIteratorIterator A RecursiveIteratorIterator
     */
    public function get_RecursiveIteratorIterator(int $mode = \RecursiveIteratorIterator::LEAVES_ONLY): \RecursiveIteratorIterator;
}

/* ------------------------------------------------------------------------- */

/*
 * Attributes are a collection of name/value pairs
 */
interface IAttribute
{

    /**
     * Set the attributes of both types
     *
     * @param array $attributes
     *            associative array of attribute name/value
     * @return null
     */
    public function set_attributes(array $attributes, array $default = []);

    /**
     * Get the attributes that are not in $compound
     *
     * @return array, current attributes
     */
    public function get_attributes(): array;

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute(string $name);

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute(string $name, $value);
}

interface IAttributeProvider
{

    public function define_attribute_default(): array;
}

/* ------------------------------------------------------------------------- */

/*
 * Compound attributes are split into two groups
 * The second group is defined by the attributes passed to set_attributes_secondary
 */
interface IAttributeSecondary extends IAttribute
{

    /**
     * Define attributes that belong to compound elements
     *
     * @param array $compound
     *            non-associative array of attribute names
     *            that exist for the compound elements
     * @return null
     */
    public function set_attributes_secondary(array $compound);

    /**
     * Get the list of defined secondary attributes
     *
     * @return array
     */
    public function get_attributes_secondary_names(): array;

    /**
     * Get the attributes that are in $compound
     *
     * @return array indexed array of the alternate values
     */
    public function get_attributes_secondary(): array;

    /**
     * Get the value of a single alternate attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute_secondary(string $name);
}

interface IAttributeSecondaryProvider extends IAttributeProvider
{

    public function define_attribute_secondary(): array;
}

/* ------------------------------------------------------------------------- */
interface IHtmlElement extends IHtmlPrinter, IHtmlPrinterList, IAttributeSecondaryProvider, IAttributeSecondary
{

    public function get_tag(): string;
}

/* ------------------------------------------------------------------------- */
interface IHtmlInputValue
{

    /**
     * Get the name of the HTML input element,
     * this is the index used to retrieve the data from POST
     *
     * @return string name of the input element
     */
    public function get_name(): string;

    /**
     * Set the contents of the input element
     * Some input elements, such as the checkbox, do not store their current
     * contents in the value attribute.
     * This routine, given the value returned by get_value(),
     * sets the appropriate attribute.
     *
     * @param mixed $value
     *            New value of the input element
     */
    public function set_value($value);

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array Validation errors, will be empty if good
     */
    public function validate(array $post): array;

    /**
     * Get this object's data in $post
     *
     * @return array,string | string contents of the input object
     */
    public function get_value(array $post);
}

interface IHtmlInput extends IHtmlInputValue
{

    /**
     * Get the type of Input
     *
     * @return string Input type
     */
    public function get_type(): string;

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id(): string;

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id(string $form_id);
}

?>