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

interface IAttributes
{
    /**
     * Get all the attributes of the field
     *
     * @return string, empty string if unset
     */
    public function get_attributes();

    /**
     * Set the attributes for this input object,
     * this overrides any previous attributes
     *
     * @return void
     */
    public function set_attributes( $attributes );

    /**
     * Get the default attributes for this input object
     *
     * @return array|fully merged list of default values
     */
    public function get_attributes_default();

    /**
     * Set the default attributes for this object
     *
     * @return null
     */
    public function set_attributes_default( $defaults );

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( $name );

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( $name, $value );

    /**
     * Extract specified attributes from current List
     *
     * @return [ [specified], [remaining] ]
     */
    public function split_attributes( $specified );
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
    public function validate( $post );
    
    /**
     * Get the errors from the most recent call to validate()
     *
     * @return array | array of errors [[ name, object, error ]] or empty
     */
    public function get_validate_errors();

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value( $post );

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id();

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( $form_id );
}

interface IHtmlPrinterList
{
    public function add_child( $child );
    public function get_child( $child );
    public function set_children( $children );
    public function get_children();
}

/*-------------------------------------------------------------------------*/
/* Manage a collection of key/value pairs (aka HTML attributes) */
/*-------------------------------------------------------------------------*/

class Attributes implements IHtmlPrinter, IAttributes
{
    static private $Unique_Identifier = 0;
    private $identity;

    public function __construct( $attributes )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        self::$Unique_Identifier += 1;
        $this->identity = self::$Unique_Identifier;
        $logger->log_var( 'Identity', $this->identity );

        if ( is_null( $attributes ) )
        {
            $attributes = [];
        }
        $this->attributes = $attributes;
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
        $logger->log_var( 'Identity', $this->identity );

        $html = self::get_html_attributes( $this->get_attributes() );

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributes routines */
    /*-------------------------------------------------------------------------*/
    
    /*
     * Current attributes
     */
    protected $attributes = [];

    /**
     * Attributes common to all elements
     *
     */
    const Global_Attributes = [
        'accesskey'             => '',
        'aria-hidden'           => False,
        'class'                 => '',
        'contenteditable'       => '',
        'dir'                   => '',
        'draggable'             => '',
        'dropzone'              => '',
        'hidden'                => False,
        'id'                    => '',
        'lang'                  => '',
        'spellcheck'            => '',
        'style'                 => '',
        'tabindex'              => '',
        'title'                 => '',
        'translate'             => '',
    ];

    /*
     * Default attributes
     */
    private $attributes_default;

    /*
     * Attributes + defaults, used to improve perf of get_attributes
     */
    private $attributes_combined_cached;

    /**
     * Get all the attributes of the field
     *
     * @return string, empty string if unset
     */
    public function get_attributes()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'Identity', $this->identity );

        // If no cached value, calculate now
        if ( is_null( $this->attributes_combined_cached ) )
        {
            $combine_defaults = $this->get_attributes_default();
            $logger->log_var( '$combine_defaults', $combine_defaults );

            $this->attributes_combined_cached = array_merge( $combine_defaults, $this->attributes );
        }

        $logger->log_return( $this->attributes_combined_cached );
        return $this->attributes_combined_cached;
    }

    /**
     * Set the attributes for this input object,
     * this overrides any previous attributes
     *
     * @return void
     */
    public function set_attributes( $attributes )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'Identity', $this->identity );

        // Clear the cached value
        $this->attributes_combined_cached = null;

        $this->attributes = $attributes;
    }

    /**
     * Get the default values for this input object
     * it recursively calls and merges all the parent's defaults as well
     *
     * @return array|fully merged list of default values
     */
    public function set_attributes_default( $defaults )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'Identity', $this->identity );
        $logger->log_var( '$defaults', $defaults );

        // Clear the cached value
        $this->attributes_combined_cached = null;

        $this->attributes_default = $defaults;
    }
    
    public function get_attributes_default()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'Identity', $this->identity );

        if ( isset( $this->attributes_default ) )
        {
            $default = array_merge( self::Global_Attributes, $this->attributes_default);
        }
        else
        {
            $default = self::Global_Attributes;
        }

        $logger->log_return( $default );
        return $default;
    }

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( $name )
    {
        $attributes = $this->get_attributes();

        $attr = '';
        if ( isset( $attributes[ $name ] ) )
        {
            $attr = $attributes[ $name ];
        }

        return $attr;
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( $name, $value )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( 'Identity', $this->identity );

        // Clear the cached value
        $this->attributes_combined_cached = null;

        if ( is_null( $this->attributes ) )
        {
            $this->attributes = [];
        }
        $this->attributes[ $name ] = $value;
    }

    /**
     * Extract specified attributes from current List
     *
     * @return [ [found], [remaining] ]
     */
    public function split_attributes( $specified )
    {
        return Helper::array_extract( $this->attributes, $specified );
    }

    /*-------------------------------------------------------------------------*/
    /* Html Helper routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the html for the specific attributes
     *
     * @return string
     */
    public static function get_html_attributes( $attributes )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $html = '';

        if ( ! empty( $attributes ) )
        {
            foreach ( $attributes as $attribute => $value )
            {
                $attribute_html = Helper::get_html_attribute( $attribute, $value );
                if ( ! empty( $attribute_html ) )
                {
                    $html .= ' ';
                    $html .= $attribute_html;
                }
            }
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* Data Access Helper routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_attribute( 'name' );
    }

    /**
     * Check if a field is required
     *
     * @param  array  $attributes
     *
     * @return boolean
     */
    public function is_required()
    {
        $required = $this->get_attribute( 'required' );

        return self::is_true( $required );
    }
}

class ElementList implements IHtmlPrinter, IHtmlPrinterList
{
    public function __construct( $children )
    {
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
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $logger->log_var( '$this', $this );

        $html = '';

        foreach ( $this->children as $child )
        {
            $logger->log_var( '$child', $child );

            $child_html = $child->get_html();
            $logger->log_var( '$child_html', $child_html );

            $html .= $child_html;
        }

        $logger->log_return( $html );
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
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( ! is_null( $child ) )
        {
            // Automatically convert strings to a HtmlText()
            if ( gettype( $child ) === 'string' )
            {
                $logger->log_msg( 'Converting to HtmlText()' );
                $child = new HtmlText( $child );
            }

            // Only allow IHtmlPrinter as children
            if ( $child instanceof IHtmlPrinter )
            {
                $logger->log_msg( 'adding $child' );
                array_push( $this->children, $child );
            }
            else
            {
                $logger->log_msg( '$child is not IHtmlPrinter' );
            }
        }
        else
        {
            $logger->log_msg( '$child is null' );
        }
    }

    /**
     * Get the childe element matching the name
     *
     * @param string $name, name of element to find
     * @return field
     */
    public function get_child( $name )
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
    public function set_children( $children )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

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

        $logger->log_return( $this->children );
    }

    /**
     * Access all defined content
     *
     * @return array of content types (HtmlText, Callback, or Element)
     */
    public function get_children()
    {
        return $this->children;
    }

}
/*-------------------------------------------------------------------------*/
/* Special types of $children */
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
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $html = '';

        if ( ! empty( $this->params ) )
        {
            $html .= call_user_func_array( $this->callback, $this->params );
        }
        else
        {
            $html .= call_user_func( $this->callback );
        }

        $logger->log_return( $html );
        return $html;
    }
}

/*-------------------------------------------------------------------------*/
/* HTML Element */
/*-------------------------------------------------------------------------*/

class Element implements IHtmlPrinter, IAttributeProvider
{
    protected $tag;                 // string
    protected $attributes;          // Attribute
    protected $children;            // ElementList

    public function __construct( $desc )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( gettype( $desc ) === 'string' )
        {
            $tag  = $desc;
            $attr = [];
            $chld = [];
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
            return;
        }

        $this->tag = $tag;
        $this->children = new ElementList( $children );

        $default    = $this->get_attributes_defaults();
        $alternate  = $this->get_attributes_alternate();
        $this->attributes = new Attributes2( $attributes, $default , $alternate );
    }

    protected function set_children( $children )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        $this->children = new ElementList( $children );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlForm routines */
    /*-------------------------------------------------------------------------*/

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
            $children       = $this->get_children();
            $alternate      = $this->get_attributes()->get_attributes_alternate();
            $logger->log_var( '$alternate', $alternate );

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
    /* Data Manipulation routines */
    /*-------------------------------------------------------------------------*/

    public function get_attributes()
    {
        return $this->attributes;
    }
    public function get_children()
    {
        return $this->children;
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

abstract class InputElement extends Element implements IHtmlForm
{
    const Alternate_Attributes = [
        'label'        ,
        'required'     ,
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
            $children       = $this->get_children();
            $alternate      = $this->get_attributes()->get_attributes_alternate();
            $remaining      = $this->get_attributes()->get_attributes();
            $logger->log_var( '$alternate', $alternate );
            $logger->log_var( '$remaining', $remaining );

            $label          = $alternate[ 'label'         ] ?? '';
            $required       = $alternate[ 'required'      ] ?? '';
            $tooltip        = $alternate[ 'data-tooltip'  ] ?? '';
            $css_container  = $alternate[ 'css-container' ] ?? '';
            $css_label      = $alternate[ 'css-label'     ] ?? '';
            
            if ( Helper::is_true( $required ) )
            {
                // Add required attribute back into input element's attributes
                $attributes[ 'required' ] = True;

                if ( !empty( $label ) )
                {
                    $label .= '<abbr class="required" title="required">&nbsp;*</abbr>';
                }
            }

            $div                            = new Element([
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
    
    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate( $post )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $name       = $this->get_attributes()->get_name();
        $required   = $this->get_attributes()->is_required();

        $this->validation_errors = [];

        if ( empty( $post ) )
        {
            $this->validation_errors[] =
            [
                'name'          => $name,
                'object'        => $this,
                'error'         => '$post is empty',
            ];
        }
        else if ( empty( $name ) )
        {
            $this->validation_errors[] =
            [
                'name'          => $name,
                'object'        => $this,
                'error'         => '$name is empty',
            ];
        }
        else if ( $required && ! isset( $post[ $name ] ) )
        {
            $this->validation_errors[] =
            [
                'name'          => $name,
                'object'        => $this,
                'error'         => '$post missing required data',
            ];
        }
        else
        {
            $this->validation_errors = $this->validate_post(  $name, $post );
        }

        return empty( $this->validation_errors );
    }

    public function get_validate_errors()
    {
        // Will fail if derived class does not implement 
        return $this->validation_errors;
    }
    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value( $post )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $cleansed = null;
        $name = $this->get_attributes()->get_name();

        if ( isset( $post[ $name ] )
             &&
             $this->validate( $post ) == null )
        {
            $cleansed = $this->cleanse( $post[ $name ] );
        }

        return $cleansed;
    }

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id()
    {
        return $this->get_attributes()->get_attribute( 'form' );
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( $form_id )
    {
        $this->get_attributes()->set_attribute( 'form', $form_id );
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
    abstract public function validate_post( $name, $post );
}
