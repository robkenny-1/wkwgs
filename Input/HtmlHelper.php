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

include_once('Constants.php');
include_once('Field.php');
include_once(__DIR__ . '/../Wkwgs_Logger.php' );

/**
 * A collection of HTML goodies/helpers
 *
 * @since 1.0.0
 */

class HtmlHelper
{
    // https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes#Attribute_list
    private static $Attribute_List = [
        // Name
        //      Type
        //      Element
        //      Note
        'accept-charset' => array(
            'string',
            "<form>",
            "List of supported charsets."
        ),
        'accept' => array(
            'string',
            "<form>, <input>",
            "List of types the server accepts, typically a file type."
        ),
        'accesskey' => array(
            'string',
            "Global attribute",
            "Defines a keyboard shortcut to activate or add focus to the element."
        ),
        'action' => array(
            'string',
            "<form>",
            "The URI of a program that processes the information submitted via the form."
        ),
        'align' => array(
            'string',
            "<applet>, <caption>, <col>, <colgroup>, <hr>, <iframe>, <img>, <table>, <tbody>, <td>, <tfoot> , <th>, <thead>, <tr>",
            "Specifies the horizontal alignment of the element."
        ),
        'allow' => array(
            'string',
            "<iframe>",
            "Specifies a feature-policy for the iframe."
        ),
        'alt' => array(
            'string',
            "<applet>, <area>, <img>, <input>",
            "Alternative text in case an image can't be displayed."
        ),
        'async' => array(
            'string',
            "<script>",
            "Indicates that the script should be executed asynchronously."
        ),
        'autocapitalize' => array(
            'string',
            "Global attribute",
            "Controls whether and how text input is automatically capitalized as it is entered/edited by the user."
        ),
        'autocomplete' => array(
            'string',
            "<form>, <input>, <textarea>",
            "Indicates whether controls in this form can by default have their values automatically completed by the browser."
        ),
        'autofocus' => array(
            'boolean',
            "<button>, <input>, <keygen>, <select>, <textarea>",
            "The element should be automatically focused after the page loaded."
        ),
        'autoplay' => array(
            'boolean',
            "<audio>, <video>",
            "The audio or video should play as soon as possible."
        ),
        'bgcolor' => array(
            'string',
            "<body>, <col>, <colgroup>, <marquee>, <table>, <tbody>, <tfoot>, <td>, <th>, <tr>",
            "Background color of the element.  Note: This is a legacy attribute. Please use the CSS background-color property instead."
        ),
        'border' => array(
            'string',
            "<img>, <object>, <table>",
            "The border width.  Note: This is a legacy attribute. Please use the CSS border property instead."
        ),
        'buffered' => array(
            'string',
            "<audio>, <video>",
            "Contains the time range of already buffered media."
        ),
        'challenge' => array(
            'string',
            "<keygen>",
            "A challenge string that is submitted along with the public key."
        ),
        'charset' => array(
            'string',
            "<meta>, <script>",
            "Declares the character encoding of the page or script."
        ),
        'checked' => array(
            'boolean',
            "<command>, <input>",
            "Indicates whether the element should be checked on page load."
        ),
        'cite' => array(
            'string',
            "<blockquote>, <del>, <ins>, <q>",
            "Contains a URI which points to the source of the quote or change."
        ),
        'class' => array(
            'string',
            "Global attribute",
            "Often used with CSS to style elements with common properties."
        ),
        'code' => array(
            'string',
            "<applet>",
            "Specifies the URL of the applet's class file to be loaded and executed."
        ),
        'codebase' => array(
            'string',
            "<applet>",
            "This attribute gives the absolute or relative URL of the directory where applets' .class files referenced by the code attribute are stored."
        ),
        'color' => array(
            'string',
            "<basefont>, <font>, <hr>",
            "This attribute sets the text color using either a named color or a color specified in the hexadecimal #RRGGBB format.  Note: This is a legacy attribute. Please use the CSS color property instead."
        ),
        'cols' => array(
            'string',
            "<textarea>",
            "Defines the number of columns in a textarea."
        ),
        'colspan' => array(
            'string',
            "<td>, <th>",
            "The colspan attribute defines the number of columns a cell should span."
        ),
        'content' => array(
            'string',
            "<meta>",
            "A value associated with http-equiv or name depending on the context."
        ),
        'contenteditable' => array(
            'string',
            "Global attribute",
            "Indicates whether the element's content is editable."
        ),
        'contextmenu' => array(
            'string',
            "Global attribute",
            "Defines the ID of a <menu> element which will serve as the element's context menu."
        ),
        'controls' => array(
            'boolean',
            "<audio>, <video>",
            "Indicates whether the browser should show playback controls to the user."
        ),
        'coords' => array(
            'string',
            "<area>",
            "A set of values specifying the coordinates of the hot-spot region."
        ),
        'crossorigin' => array(
            'string',
            "<audio>, <img>, <link>, <script>, <video>",
            "How the element handles cross-origin requests"
        ),
        'csp' => array(
            'string',
            "<iframe>",
            "Specifies the Content Security Policy that an embedded document must agree to enforce upon itself."
        ),
        //'data-*' => array(
        //    'string',
        //    "Global attribute",
        //    "Lets you attach custom attributes to an HTML element."
        //),
        'data' => array(
            'string',
            "<object>",
            "Specifies the URL of the resource."
        ),
        'datetime' => array(
            'string',
            "<del>, <ins>, <time>",
            "Indicates the date and time associated with the element."
        ),
        'decoding' => array(
            'string',
            "<img>",
            "Indicates the preferred method to decode the image."
        ),
        'default' => array(
            'string',
            "<track>",
            "Indicates that the track should be enabled unless the user's preferences indicate something different."
        ),
        'defer' => array(
            'boolean',
            "<script>",
            "Indicates that the script should be executed after the page has been parsed."
        ),
        'dir' => array(
            'string',
            "Global attribute",
            "Defines the text direction. Allowed values are ltr (Left-To-Right) or rtl (Right-To-Left)"
        ),
        'dirname' => array(
            'string',
            "<input>, <textarea>",
            ""
        ),
        'disabled' => array(
            'boolean',
            "<button>, <command>, <fieldset>, <input>, <keygen>, <optgroup>, <option>, <select>, <textarea>",
            "Indicates whether the user can interact with the element."
        ),
        'download' => array(
            'boolean',
            "<a>, <area>",
            "Indicates that the hyperlink is to be used for downloading a resource."
        ),
        'draggable' => array(
            'string',
            "Global attribute",
            "Defines whether the element can be dragged."
        ),
        'dropzone' => array(
            'string',
            "Global attribute",
            "Indicates that the element accept the dropping of content on it."
        ),
        'enctype' => array(
            'string',
            "<form>",
            "Defines the content type of the form date when the method is POST."
        ),
        'for' => array(
            'string',
            "<label>, <output>",
            "Describes elements which belongs to this one."
        ),
        'form' => array(
            'string',
            "<button>, <fieldset>, <input>, <keygen>, <label>, <meter>, <object>, <output>, <progress>, <select>, <textarea>",
            "Indicates the form that is the owner of the element."
        ),
        'formaction' => array(
            'string',
            "<input>, <button>",
            "Indicates the action of the element, overriding the action defined in the <form>."
        ),
        'headers' => array(
            'string',
            "<td>, <th>",
            "IDs of the <th> elements which applies to this element."
        ),
        'height' => array(
            'string',
            "<canvas>, <embed>, <iframe>, <img>, <input>, <object>, <video>",
            "Specifies the height of elements listed here. For all other elements, use the CSS height property.  Note: In some instances, such as <div>, this is a legacy attribute, in which case the CSS height property should be used instead."
        ),
        'hidden' => array(
            'boolean',
            "Global attribute",
            "Prevents rendering of given element, while keeping child elements, e.g. script elements, active."
        ),
        'high' => array(
            'string',
            "<meter>",
            "Indicates the lower bound of the upper range."
        ),
        'href' => array(
            'string',
            "<a>, <area>, <base>, <link>",
            "The URL of a linked resource."
        ),
        'hreflang' => array(
            'string',
            "<a>, <area>, <link>",
            "Specifies the language of the linked resource."
        ),
        'http-equiv' => array(
            'string',
            "<meta>",
            "Defines a pragma directive."
        ),
        'icon' => array(
            'string',
            "<command>",
            "Specifies a picture which represents the command."
        ),
        'id' => array(
            'string',
            "Global attribute",
            "Often used with CSS to style a specific element. The value of this attribute must be unique."
        ),
        'importance ' => array(
            'string',
            "<iframe>, <img>, <link>, <script>",
            "Indicates the relative fetch priority for the resource."
        ),
        'integrity' => array(
            'string',
            "<link>, <script>",
            "Security Feature that allows browsers to verify what they fetch."
        ),
        'ismap' => array(
            'boolean',
            "<img>",
            "Indicates that the image is part of a server-side image map."
        ),
        'itemprop' => array(
            'string',
            "Global attribute",
            ""
        ),
        'keytype' => array(
            'string',
            "<keygen>",
            "Specifies the type of key generated."
        ),
        'kind' => array(
            'string',
            "<track>",
            "Specifies the kind of text track."
        ),
        'label' => array(
            'string',
            "<track>",
            "Specifies a user-readable title of the text track."
        ),
        'lang' => array(
            'string',
            "Global attribute",
            "Defines the language used in the element."
        ),
        'language' => array(
            'string',
            "<script>",
            "Defines the script language used in the element."
        ),
        'lazyload ' => array(
            'string',
            "<img>, <iframe>",
            "Indicates if the element should be loaded lazily."
        ),
        'list' => array(
            'string',
            "<input>",
            "Identifies a list of pre-defined options to suggest to the user."
        ),
        'loop' => array(
            'boolean',
            "<audio>, <bgsound>, <marquee>, <video>",
            "Indicates whether the media should start playing from the start when it's finished."
        ),
        'low' => array(
            'string',
            "<meter>",
            "Indicates the upper bound of the lower range."
        ),
        'manifest' => array(
            'string',
            "<html>",
            "Specifies the URL of the document's cache manifest."
        ),
        'max' => array(
            'string',
            "<input>, <meter>, <progress>",
            "Indicates the maximum value allowed."
        ),
        'maxlength' => array(
            'string',
            "<input>, <textarea>",
            "Defines the maximum number of characters allowed in the element."
        ),
        'media' => array(
            'string',
            "<a>, <area>, <link>, <source>, <style>",
            "Specifies a hint of the media for which the linked resource was designed."
        ),
        'method' => array(
            'string',
            "<form>",
            "Defines which HTTP method to use when submitting the form. Can be GET (default) or POST."
        ),
        'min' => array(
            'string',
            "<input>, <meter>",
            "Indicates the minimum value allowed."
        ),
        'minlength' => array(
            'string',
            "<input>, <textarea>",
            "Defines the minimum number of characters allowed in the element."
        ),
        'multiple' => array(
            'boolean',
            "<input>, <select>",
            "Indicates whether multiple values can be entered in an input of the type email or file."
        ),
        'muted' => array(
            'boolean',
            "<audio>, <video>",
            "Indicates whether the audio will be initially silenced on page load."
        ),
        'name' => array(
            'string',
            "<button>, <form>, <fieldset>, <iframe>, <input>, <keygen>, <object>, <output>, <select>, <textarea>, <map>, <meta>, <param>",
            "Name of the element. For example used by the server to identify the fields in form submits."
        ),
        'novalidate' => array(
            'boolean',
            "<form>",
            "This attribute indicates that the form shouldn't be validated when submitted."
        ),
        'open' => array(
            'string',
            "<details>",
            "Indicates whether the details will be shown on page load."
        ),
        'optimum' => array(
            'string',
            "<meter>",
            "Indicates the optimal numeric value."
        ),
        'pattern' => array(
            'string',
            "<input>",
            "Defines a regular expression which the element's value will be validated against."
        ),
        'ping' => array(
            'string',
            "<a>, <area>",
            ""
        ),
        'placeholder' => array(
            'string',
            "<input>, <textarea>",
            "Provides a hint to the user of what can be entered in the field."
        ),
        'poster' => array(
            'string',
            "<video>",
            "A URL indicating a poster frame to show until the user plays or seeks."
        ),
        'preload' => array(
            'string',
            "<audio>, <video>",
            "Indicates whether the whole resource, parts of it or nothing should be preloaded."
        ),
        'radiogroup' => array(
            'string',
            "<command>",
            ""
        ),
        'readonly' => array(
            'boolean',
            "<input>, <textarea>",
            "Indicates whether the element can be edited."
        ),
        'rel' => array(
            'string',
            "<a>, <area>, <link>",
            "Specifies the relationship of the target object to the link object."
        ),
        'required' => array(
            'boolean',
            "<input>, <select>, <textarea>",
            "Indicates whether this element is required to fill out or not."
        ),
        'reversed' => array(
            'string',
            "<ol>",
            "Indicates whether the list should be displayed in a descending order instead of a ascending."
        ),
        'rows' => array(
            'string',
            "<textarea>",
            "Defines the number of rows in a text area."
        ),
        'rowspan' => array(
            'string',
            "<td>, <th>",
            "Defines the number of rows a table cell should span over."
        ),
        'sandbox' => array(
            'string',
            "<iframe>",
            "Stops a document loaded in an iframe from using certain features (such as submitting forms or opening new windows)."
        ),
        'scope' => array(
            'string',
            "<th>",
            "Defines the cells that the header test (defined in the th element) relates to."
        ),
        'scoped' => array(
            'string',
            "<style>",
            ""
        ),
        'selected' => array(
            'boolean',
            "<option>",
            "Defines a value which will be selected on page load."
        ),
        'shape' => array(
            'string',
            "<a>, <area>",
            ""
        ),
        'size' => array(
            'string',
            "<input>, <select>",
            "Defines the width of the element (in pixels). If the element's type attribute is text or password then it's the number of characters."
        ),
        'sizes' => array(
            'string',
            "<link>, <img>, <source>",
            ""
        ),
        'slot' => array(
            'string',
            "Global attribute",
            "Assigns a slot in a shadow DOM shadow tree to an element."
        ),
        'span' => array(
            'string',
            "<col>, <colgroup>",
            ""
        ),
        'spellcheck' => array(
            'string',
            "Global attribute",
            "Indicates whether spell checking is allowed for the element."
        ),
        'src' => array(
            'string',
            "<audio>, <embed>, <iframe>, <img>, <input>, <script>, <source>, <track>, <video>",
            "The URL of the embeddable content."
        ),
        'srcdoc' => array(
            'string',
            "<iframe>",
            ""
        ),
        'srclang' => array(
            'string',
            "<track>",
            ""
        ),
        'srcset' => array(
            'string',
            "<img>, <source>",
            "One or more responsive image candidates."
        ),
        'start' => array(
            'string',
            "<ol>",
            "Defines the first number if other than 1."
        ),
        'step' => array(
            'string',
            "<input>",
            ""
        ),
        'style' => array(
            'string',
            "Global attribute",
            "Defines CSS styles which will override styles previously set."
        ),
        'summary' => array(
            'string',
            "<table>",
            ""
        ),
        'tabindex' => array(
            'string',
            "Global attribute",
            "Overrides the browser's default tab order and follows the one specified instead."
        ),
        'target' => array(
            'string',
            "<a>, <area>, <base>, <form>",
            ""
        ),
        'title' => array(
            'string',
            "Global attribute",
            "Text to be displayed in a tooltip when hovering over the element."
        ),
        'translate' => array(
            'string',
            "Global attribute",
            "Specify whether an element’s attribute values and the values of its Text node children are to be translated when the page is localized, or whether to leave them unchanged."
        ),
        'type' => array(
            'string',
            "<button>, <input>, <command>, <embed>, <object>, <script>, <source>, <style>, <menu>",
            "Defines the type of the element."
        ),
        'usemap' => array(
            'string',
            "<img>, <input>, <object>",
            ""
        ),
        'value' => array(
            'string',
            "<button>, <option>, <input>, <li>, <meter>, <progress>, <param>",
            "Defines a default value which will be displayed in the element on page load."
        ),
        'width' => array(
            'string',
            "<canvas>, <embed>, <iframe>, <img>, <input>, <object>, <video>",
            "For the elements listed here, this establishes the element's width.  Note: For all other instances, such as <div>, this is a legacy attribute, in which case the CSS width property should be used instead."
        ),
        'wrap' => array(
            'string',
            "<textarea>",
            "Indicates whether the text should be wrapped." 
        ),
    ];

    private static $Attribute_boolean = null;

    public static function get_boolean_attributes()
    {
        if ( is_null( self::$Attribute_boolean ) )
        {
            self::$Attribute_boolean = array();
            foreach ( self::$Attribute_List as $attr => $value )
            {
                if ( $value[0] === 'boolean' )
                {
                    array_push( self::$Attribute_boolean, $attr );
                }
            }
        }

        return self::$Attribute_boolean;
    }

    public static function print_attribute( $attr, $value, $exclude = null )
    {
        // Since attributes and values are not user-generated
        // we should not need to cleanse their values

        if ( gettype( $exclude ) === 'array' && in_array( $attr, $exclude ) )
        {
            return;
        }

        /*
         * Boolean attributes, when set, are specified in only 1 of 3 ways
         * When unset the attribute *must not* be present
         * <input type=checkbox  name=cheese checked />
         * <input type=checkbox  name=cheese checked='' />
         * <input type=checkbox  name=cheese checked='checked' />
         */
        $attr_is_boolean = in_array( $attr, HtmlHelper::get_boolean_attributes() );
        if ( $attr_is_boolean && self::is_true( $value ) )
        {
            echo $attr . PHP_EOL;
            return;
        }

        if ( ! empty( $value ) )
        {
            echo $attr . '="' . $value . '"' . PHP_EOL;
            return;
        }
    }

    /**
     * Does the content of the string equate to a True value
     * Does not rely on type conversion,
     * it uses a whitelist of acceptable values for True,
     * all other values are False
     *
     * @return True if $val is a true value
     */
    public static function is_true( $val )
    {
        if ( gettype( $val ) === 'boolean' )
        {
            return $val;
        }

        if ( gettype( $val ) === 'string' )
        {
            $val = strtolower( $val );

            return in_array( $val, [ 'yes', '1', 'true' ] );
        }

        return False;
    }

}
