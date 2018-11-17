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

include_once('Input.php');

/*-------------------------------------------------------------------------*/
/* Classes */
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

    public function get_attributes() : IAttributes
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        //$logger->log_var( '$this->tag', $this->tag );
        //$logger->log_var( '$this->attributes', $this->attributes );

        return $this->attributes;
    }

    /*-------------------------------------------------------------------------*/
    /* \Input\IHtmlPrinter routines */
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
            $html .= "<{$this->tag}";
            $html .= $this->get_attributes()->get_html();
            $html .= '>';
            if ( ! Helper::is_void_element( $this->tag ) )
            {
                $html .= $this->children->get_html();
                $html .= "</{$this->tag}>";
            }
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* \Input\IHtmlPrinterList routines */
    /*-------------------------------------------------------------------------*/

    public function add_child( $child )
    {
        $this->children->add_child( $child );
    }

    /*-------------------------------------------------------------------------*/
    /* \Iterator routines */
    /*-------------------------------------------------------------------------*/

    function rewind()
    {
        return $this->children->rewind();
    }
    function current()
    {
        return $this->children->current();
    }
    function key()
    {
        return $this->children->key();
    }
    function next()
    {
        return $this->children->next();
    }
    function valid()
    {
        return $this->children->valid();
    }

    /*-------------------------------------------------------------------------*/
    /* \Input\IHtmlElement routines */
    /*-------------------------------------------------------------------------*/

    public function get_tag() : string
    {
        return $this->tag;
    }

    public function get_name() : string
    {
        return $this->get_attributes()->get_attribute( 'name' );
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
 */
abstract class InputElement extends Element implements IHtmlFormElementElement
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
                            new HtmlText( $label ),
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
    /* IHtmlFormElementElement routines */
    /*-------------------------------------------------------------------------*/

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

        $validation_errors = [];

        // These first three errors preclude all others
        if ( empty( $name ) )
        {
            $validation_errors[] = new HtmlValidateError(
                '$name is empty', $name, $this
            );
        }
        else if ( empty( $post ) )
        {
            $validation_errors[] = new HtmlValidateError(
                '$post is empty', $name, $this
            );
        }
        else if ( $required && empty( $raw ) )
        {
            $validation_errors[] = new HtmlValidateError(
                '$post missing required data', $name, $this
            );
        }
        else
        {
            if ( !empty($pattern) && ! filter_var( $raw, FILTER_VALIDATE_REGEXP, $pattern ) )
            {
                $validation_errors[] = new HtmlValidateError(
                    'value does not match defined pattern', $name, $this
                );
            }

            $ve = $this->validate_post( $name, $post );
            $validation_errors = array_merge($validation_errors, $ve);
        }

        //$logger->log_return( $validation_errors );
        return $validation_errors;
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
