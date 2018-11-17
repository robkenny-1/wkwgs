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

include_once('HtmlHelper.php');

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
    public function get_html();
}

interface IHtmlPrinterList
{
    public function add_child( $child );
    public function get_child( $child ) : IHtmlElement;
    public function set_children( array $children );
    public function get_children() : array;
}

interface IHtmlElement extends IHtmlPrinter, IHtmlPrinterList
{
    public function get_tag() : string;
    public function get_name() : string;
}

interface IHtmlForm
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
     * Get the errors from the most recent call to validate()
     *
     * @return array | array of errors [[ name, object, error ]] or empty
     */
    public function get_validate_errors() : array;

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

interface IHtmlValidateError
{
    public function get_error() : string;
    public function get_object() : ?IHtmlElement;
    public function get_name() : string;
}

/*-------------------------------------------------------------------------*/
/* Manage a collection of key/value pairs (aka HTML attributes) */
/*-------------------------------------------------------------------------*/

class ElementList implements IHtmlPrinter, IHtmlPrinterList
{
    public function __construct( $children )
    {
        if ( gettype( $children ) === 'string' )
        {
            $children = [ new HtmlText( $children ) ];
        }
        $this->set_children( $children );
    }
    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html()
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        //$logger->log_var( '$this', $this );

        $html = '';

        foreach ( $this->children as $child )
        {
            //$logger->log_var( '$child', $child );

            $child_html = $child->get_html();
            //$logger->log_var( '$child_html', $child_html );

            $html .= $child_html;
        }

        //$logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinterList routines */
    /*-------------------------------------------------------------------------*/

    protected $children = [];

    /**
     * Append a single content item
     *
     * @return null
     */
    public function add_child( $child )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( ! is_null( $child ) )
        {
            // Automatically convert strings to a HtmlText()
            if ( gettype( $child ) === 'string' )
            {
                //$logger->log_msg( 'Converting to HtmlText()' );
                $child = new HtmlText( $child );
            }

            // Only allow IHtmlPrinter as children
            if ( $child instanceof IHtmlPrinter )
            {
                //$logger->log_msg( 'adding $child' );
                array_push( $this->children, $child );
            }
            else
            {
                //$logger->log_msg( '$child is not IHtmlPrinter' );
            }
        }
        else
        {
            //$logger->log_msg( '$child is null' );
        }
    }

    /**
     * Get the childe element matching the name
     *
     * @param string $name, name of element to find
     * @return field
     */
    public function get_child( $name ) : IHtmlElement
    {
        foreach ( $this->get_fields() as $field )
        {
            if ( $field->get_name() === $name )
            {
                return $field;
            }
        }
    }

    /**
     * Replace all content with the new values
     *
     * @return null
     */
    public function set_children( array $children )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->children = [];

        if ( $children instanceof IHtmlPrinterList )
        {
            $children = $children->get_children();
        }

        if ( gettype( $children ) === 'array' )
        {
            foreach ( $children as $child )
            {
                $this->add_child( $child );
            }
        }
        else
        {
            $this->add_child( $children );
        }

        //$logger->log_return( $this->children );
    }

    /**
     * Access all defined content
     *
     * @return array of content types (HtmlText, Callback, or Element)
     */
    public function get_children() : array
    {
        return $this->children;
    }
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
/*-------------------------------------------------------------------------*/
/* Special types of children */
/*-------------------------------------------------------------------------*/

class HtmlText implements IHtmlPrinter
{
    protected $text;

    public function __construct( $text )
    {
        $this->text = $text;
    }
    public function get_html()
    {
        return $this->text;
    }
}

class Callback implements IHtmlPrinter
{
    protected   $callback   = '';
    protected   $params     = null;

    public function __construct( $callback, $params = null )
    {
        $this->callback     = $callback;
        $this->params       = $params;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html()
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $html = '';

        if ( ! empty( $this->params ) )
        {
            $html .= call_user_func_array( $this->callback, $this->params );
        }
        else
        {
            $html .= call_user_func( $this->callback );
        }

        //$logger->log_return( $html );
        return $html;
    }
}

/*-------------------------------------------------------------------------*/
/* HTML Element */
/*-------------------------------------------------------------------------*/


/*
* class should implement IAttributes,
* current get_attributes() should be renamed get_attributes_handler()
* similar for IHtmlPrinterList
*/





class Element implements IHtmlElement, IAttributeProvider
{
    protected $tag;                 // string
    protected $attributes;          // Attribute
    protected $children;            // ElementList

    public function __construct( $desc )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( gettype( $desc ) === 'string' )
        {
            $tag        = $desc;
            $attributes = [];
            $children   = [];
        }
        else
        {
            $tag        = $desc[ 'tag'        ] ?? '';
            $attributes = $desc[ 'attributes' ] ?? [];
            $children   = $desc[ 'contents'   ] ?? [];
        }
        
        if ( empty( $tag ) )
        {
            $logger->log_msg( '$tag is empty');
        }

        $this->tag = $tag;
        $this->children = new ElementList( $children );

        $default    = $this->get_attributes_defaults();
        $alternate  = $this->get_attributes_alternate();
        $this-> attributes = new Attributes( $attributes, $default , $alternate );
    }

    public function get_attributes_defaults() : array
    {
        return [];
    }

    public function get_attributes_alternate() : array
    {
        return [];
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'tag', $this->tag );

        $html = '';

        if ( ! empty( $this->tag ) )
        {
            $children       = $this->children;

            $html .= "<{$this->tag}";
            $html .= $this->get_attributes()->get_html();
            $html .= '>';
            if ( ! Helper::is_void_element( $this->tag ) )
            {
                $html .= $children->get_html();
                $html .= "</{$this->tag}>";
            }
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlElement routines */
    /*-------------------------------------------------------------------------*/
    
    public function get_tag() : string
    {
        return $this->tag;
    }
    
    public function get_name() : string
    {
        return $this->get_attributes()->get_attribute( 'name' );
    }

    public function add_child( $child )
    {
        $this->children->add_child( $child );
    }

    public function get_child( $child ) : IHtmlElement
    {
        return $this->children->get_child( $child );
    }

    public function set_children( array $children )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        $this->children = new ElementList( $children );
    }

    public function get_children() : array
    {
        return $this->children->get_children();    
    }

    public function get_attributes() : IAttributes
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        //$logger->log_var( '$this->tag', $this->tag );
        //$logger->log_var( '$this->attributes', $this->attributes );

        return $this->attributes;
    }


    /*-------------------------------------------------------------------------*/
    /* Helper routines for HTML */
    /*-------------------------------------------------------------------------*/

    /**
     * Echo this objects HTML string to output
     *
     * @return null
     */
    public function render()
    {
        $html = $this->get_html();
        echo $html;
    }
}

/*
 * The InputElement Class
 *
 * Layout of the input element
 *  <div class="css-container">
 *     <label class="css-label">
 *       Label Text
 *       <input class="css-input" />
 *     </label>
 *  </div>
 *
 * You don't need all three class definitions to style the elements
 * CSS Example:
 *
 * .css-container {
 *   background-color: Blue;
 * }
 * .css-container label {
 *   background-color: LightYellow;
 * }
 * 
 * .css-container input {
 *   background-color: SeaGreen;
 * }
 * 
 */

 abstract class InputElement extends Element implements IHtmlForm
{
    const Alternate_Attributes = [
        'label'        ,
        'data-tooltip' ,
        'css-container',
        'css-label'    ,
        'css-input'    ,
    ];

    public function get_type()
    {
        return $this->get_attributes()->get_attribute( 'type' );
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeProvider routines */
    /*-------------------------------------------------------------------------*/

    public function get_attributes_defaults() : array
    {
        return [];
    }

    public function get_attributes_alternate() : array
    {
        return self::Alternate_Attributes;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * Layout of the output field
     *  <div class="css-container">
     *     <label class="css-label">
     *       Label Text
     *       <input class="css-input" />
     *     </label>
     *  </div>
     *
     * @return string
     */
    public function get_html()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'tag',  $this->tag );
        $logger->log_var( 'type', $this->get_type());

        $html = '';

        if ( ! empty( $this->tag ) )
        {
            $alternate      = $this->get_attributes()->get_attributes_alternate();
            $attributes     = $this->get_attributes()->get_attributes();
            $logger->log_var( '$alternate', $alternate );
            $logger->log_var( '$attributes', $attributes );

            $required       = $attributes[ 'required'      ] ?? '';
            $label          = $alternate [ 'label'         ] ?? '';
            $tooltip        = $alternate [ 'data-tooltip'  ] ?? '';
            $css_container  = $alternate [ 'css-container' ] ?? '';
            $css_label      = $alternate [ 'css-label'     ] ?? '';
            
            if ( Helper::is_true( $required ) )
            {
                // Add required attribute back into input element's attributes
                $attributes[ 'required' ] = True;

                if ( !empty( $label ) )
                {
                    $label .= '<abbr class="required" title="required">&nbsp;*</abbr>';
                }
            }

            $logger->log_msg( 'Creating Element for output' );

            $div = new Element([
                'tag'                       => 'div',
                'attributes'                => [ 'class' => $css_container ],
                'contents'                  => [
                    new Element([
                        'tag'               => 'label',
                        'attributes'        => [
                            'class'         => $css_label,
                            'data-tooltip'  => $tooltip,
                        ],
                        'contents'          => [
                            $label,
                            new Callback( [ $this, 'get_html_just_me' ] ),
                        ]
                    ])
                ]
            ]);

             $html = $div->get_html();
        }

        $logger->log_return( $html );
        return $html;
    }

    public function get_html_just_me()
    {
        return parent::get_html();
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlForm routines */
    /*-------------------------------------------------------------------------*/

    protected $validation_errors = [];
    
    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate( array $post ) : array
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $name       = $this->get_name();
        $raw        = $post[ $name ] ?? '';
        $required   = Helper::is_true( $this->get_attributes()->get_attribute( 'required' ) );
        $pattern    = Helper::is_true( $this->get_attributes()->get_attribute( 'pattern' ) );

        $this->validation_errors = [];
        $validation_errors = [];
        
        // These first three errors preclude all others
        if ( empty( $name ) )
        {
            $this->validation_errors[] = new HtmlValidateError(
                '$name is empty', $name, $this                
            );
        }
        else if ( empty( $post ) )
        {
            $this->validation_errors[] = new HtmlValidateError(
                '$post is empty', $name, $this                
            );
        }
        else if ( $required && empty( $raw ) )
        {
            $this->validation_errors[] = new HtmlValidateError(
                '$post missing required data', $name, $this                
            );
        }
        else
        {
            if ( !empty($pattern) && ! filter_var( $raw, FILTER_VALIDATE_REGEXP, $pattern ) )
            {
                $this->validation_errors[] = new HtmlValidateError(
                    'value does not match defined pattern', $name, $this                
                );
            }

            $ve = $this->validate_post( $name, $post );
            $this->validation_errors = array_merge($this->validation_errors, $ve);
        }

        //$logger->log_return( $this->validation_errors );
        return $this->validation_errors;
    }

    public function get_validate_errors() : array
    {
        return $this->validation_errors ?? [];
    }

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value( array $post )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $cleansed = null;
        $name = $this->get_name();

        if ( isset( $post[ $name ] )
             &&
             empty( $this->validate( $post )) )
        {
            $cleansed = $this->cleanse_data( $post[ $name ] );
        }

        return $cleansed;
    }

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id() : string
    {
        return $this->get_attributes()->get_attribute( 'form' );
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( string $form_id )
    {
        $this->attributes->set_attribute( 'form', $form_id );
    }

    /*-------------------------------------------------------------------------*/
    /* InputElement routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    abstract public function cleanse_data( $raw );

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    abstract public function validate_post( string $name, array $post ) : array;
}
