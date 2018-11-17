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

/**
 * The text input Class
 *
 * @since 1.0.0
 */
class Button extends Element
{
    const Tag_Type = 'button';
    const Default_Attributes    = [
        'type'          => 'submit',
    ];
    const Alternate_Attributes = [
        'label'        ,
        'required'     ,
        'data-tooltip' ,
        'css-container',
        'css-label'    ,
        'css-input'    ,
    ];

    public function __construct( $desc )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        if ( gettype( $desc ) !== 'array' )
        {
            $logger->log_var( '$desc is not an array', $desc );
            return;
        }

        $desc[ 'tag' ] = self::Tag_Type;
        parent::__construct( $desc );
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeProvider routines */
    /*-------------------------------------------------------------------------*/

    public function get_attributes_defaults() : array
    {
        $parent = parent::get_attributes_defaults();
        return array_merge( $parent, self::Default_Attributes );
    }

    public function get_attributes_alternate() : array
    {
        $parent = parent::get_attributes_alternate();
        return array_merge( $parent, self::Alternate_Attributes );
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
        $logger->log_var( 'tag', $this->tag );

        $html = '';

        if ( ! empty( $this->tag ) )
        {
            $alternate      = $this->get_attributes()->get_attributes_alternate();
            $remaining      = $this->get_attributes()->get_attributes();
            $logger->log_var( '$alternate', $alternate );
            $logger->log_var( '$remaining', $remaining );

            $label = $alternate[ 'label' ] ?? '';

            $button = new Element([
                'tag'               => 'button',
                'attributes'        => $remaining,
                'contents'          => [ new HtmlText($label) ]
            ]);

             $html = $button->get_html();
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlFormElement routines */
    /*-------------------------------------------------------------------------*/

    /* all handled by InputElement */

    /*-------------------------------------------------------------------------*/
    /* InputElement routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Validate data for a button
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post( string $name, array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $validation_errors = [];

        // The button should only return 'value'
        if ( $post[ $name ] !== $this->get_attributes()->get_attribute( 'value' ) )
        {
            $validation_errors[] = new HtmlValidateError(
                '$post post data does not match expected', $name, $this
            );
        }

        $logger->log_return( $validation_errors );
        return $validation_errors;
    }

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function cleanse_data( $raw )
    {
        // no cleansing necessary
        return $raw;
    }
}