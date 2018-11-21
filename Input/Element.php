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
* class should implement IAttribute,
* current get_attributes() should be renamed get_attributes_handler()
* similar for IHtmlPrinterList
*/

class Element implements IHtmlElement
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
            $msg = '$tag is empty';
            $logger->log_msg( $msg );
            throw new \Exception( $msg );
        }

        $this->tag = $tag;
        $this->children = new ElementList( $children );

        $default    = $this->define_attribute_default();
        $compound   = $this->define_attribute_seconday();
        $this-> attributes = new Attributes( $attributes, $default , $compound );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlElement routines */
    /*-------------------------------------------------------------------------*/

    public function get_tag() : string
    {
        return $this->tag;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html() : string
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $tag = $this->tag;
        $tag = htmlspecialchars($tag);
        $logger->log_var( 'tag', $this->tag );

        $html = '';

        $html .= "<$tag";
        $html .= $this->attributes->get_html();
        $html .= '>';
        if ( ! Helper::is_void_element( $this->tag ) )
        {
            $html .= $this->children->get_html();
            $html .= "</$tag>";
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinterList routines */
    /*-------------------------------------------------------------------------*/

    public function add_child( $child )
    {
        $this->children->add_child( $child );
    }

    /*-------------------------------------------------------------------------*/
    /* Iterator routines */
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
    /* IAttribute routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Set the attributes of both types
     *
     * @param array $attributes associative array of attribute name/value
     * @return null
     */
    public function set_attributes( array $attributes, array $default = [] )
    {
        $this->attributes->set_attributes( $attributes, $default );
    }

    /**
     * Get the attributes that are not in $compound
     *
     * @return array, current attributes
     */
    public function get_attributes() : array
    {
        return $this->attributes->get_attributes();
    }

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute( string $attribute )
    {
        return $this->attributes->get_attribute( $attribute );
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $attribute, $value )
    {
        $this->attributes->set_attribute( $attribute, $value );
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeSeconday routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the attributes that are in $compound
     *
     * @return indexed array of the alternate values
     */
    public function get_attributes_seconday() : array
    {
        return $this->attributes->get_attributes_seconday();
    }

    /**
     * Get the value of a single alternate attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute_secondary( string $attribute )
    {
        return $this->attributes->get_attribute_secondary( $attribute );
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeSecondayProvider routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Define attributes that belong to compound elements
     *
     * @param array $compound non-associative array of attribute names
     * that exist for the compound elements
     * @return null
     */
    public function set_attribute_seconday( array $compound )
    {
        $this->attributes->set_attribute_seconday( $compound );
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeProvider routines */
    /*-------------------------------------------------------------------------*/

    public function define_attribute_default() : array
    {
        return [];
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeSecondayProvider routines */
    /*-------------------------------------------------------------------------*/

    public function define_attribute_seconday() : array
    {
        return [];
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
 *  <div class="container-class">
 *     <label class="label-class">
 *       Label Text
 *       <input type='text' />
 *     </label>
 *  </div>
 *
 */
abstract class InputElement extends Element implements IHtmlInput
{
    const Attributes_Default    = [
        'type'                  => 'text',
        'container-tag'         => 'div',
    ];
    const Attributes_Secondary = [
        'label-',
        'container-',
    ];

    /*-------------------------------------------------------------------------*/
    /* IAttributeProvider routines */
    /*-------------------------------------------------------------------------*/

    public function define_attribute_default() : array
    {
        return self::Attributes_Default;
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeSecondayProvider routines */
    /*-------------------------------------------------------------------------*/

    public function define_attribute_seconday() : array
    {
        return self::Attributes_Secondary;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * Layout of the output field
     *  <div class="container-class">
     *     <label class="css-label">
     *       Label Text
     *       <input class="class" />
     *     </label>
     *  </div>
     *
     * @return string
     */
    public function get_html() : string
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'tag', $this->tag );
        $logger->log_var( 'type',  $this->get_type() );

        $label_attributes       = $this->get_attributes_seconday()[ 'label' ];
        $container_attributes   = $this->get_attributes_seconday()[ 'container' ];
        $container_tag          = Attributes::get_attribute_and_remove( 'tag',  $container_attributes);
        $label_text             = Attributes::get_attribute_and_remove( 'text', $label_attributes);

        $logger->log_var( '$label_attributes',      $label_attributes );
        $logger->log_var( '$container_attributes',  $container_attributes );

        if ( !empty($label_text) && Helper::is_true($this->get_attributes( 'required' )))
        {
            $label_text .= '<abbr class="required" title="required">&nbsp;*</abbr>';
        }

        $label_contents = [];
        if ( !empty($label_text))
        {
            $label_contents[] = new HtmlText( $label_text );
        }
        $label_contents[] = new \Input\Callback( [ $this, 'get_html_core' ] );

        $compound = new Element([
            'tag'                       => $container_tag,
            'attributes'                => $container_attributes,
            'contents'                  => [
                new Element([
                    'tag'               => 'label',
                    'attributes'        => $label_attributes,
                    'contents'          => $label_contents
                ])
            ]
        ]);    

        $html = $compound->get_html();

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlInput routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the type of Input
     *
     * @return  string Input type
     */
    public function get_type() : string
    {
        return $this->get_attribute( 'type' );
    }

    /**
     * Get the name of the HTML input element,
     * this is the index used to retrieve the data from POST
     *
     * @return string name of the input element
     */
    public function get_name() : string
    {
        return $this->get_attribute( 'name' );
    }

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate( array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $name       = $this->get_name();
        $raw        = $post[ $name ] ?? '';
        $required   = Helper::is_true( $this->get_attribute( 'required' ) );
        $pattern    = $this->get_attribute('pattern');

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
            if ( !empty($pattern) && !empty( $raw ))
            {
                $delim = '#';
                $pattern = $delim . addcslashes($pattern, $delim) . $delim;

                if (preg_match($pattern, $raw) !== 1)
                {
                    $logger->log_msg('$raw does not match pattern');

                    $validation_errors[] = new HtmlValidateError(
                        'value does not match defined pattern', $name, $this
                    );
                }
            }

            $ve = $this->validate_post( $name, $post );
            $validation_errors = array_merge($validation_errors, $ve);
        }

        $logger->log_return( $validation_errors );
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
        return $this->get_attribute( 'form' );
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

    /**
     * Get the HTML for the core (<input>) object
     * This is used by get_html
     *
     * @return string HTML of the <input> element
     */
    public function get_html_core() : string
    {
        return parent::get_html();
    }
}
