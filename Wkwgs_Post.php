<?php
/*
    "Washington Koi & Water Garden Society" Copyright (C) 2018 Rob Kenny

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

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

namespace WKWGS;

/*
* This class is used to assist in the extraction of input field Database
* on the server (from POST data)
* Extra caution is used to validate and cleanse the (untrustworthy) data
*/
class WooCommerce_InputHelper
{
    /* Consider adding support for nonce */

    $Label = null;
    $Desc  = null;
    $Post  = null;

    /**
     * Create an instance of this helper for a form field
     * see woocommerce_form_field()
     */
    public __construct($label, $desc, $post = $_POST)
    {
        $this->Label = $label;
        $this->Desc  = $desc;
        $this->Post  = $post;
    }

    /**
     * return if this field requires data
     * @return bool
     */
    public function is_required()
    {
        return empty($this->Desc['required']);
    }

    /**
     * Return the validation requirements
     * @return array
     */
    public function get_validate()
    {
        return $this->Desc['validate'];
    }

    /**
     * Return the validation requirements
     * @return array
     */
    public function get_type()
    {
        return $this->Desc['type'];
    }

    /**
     * Return the validation requirements
     * @return array
     */
    public function get_label()
    {
        return $this->Desc['label'];
    }

    /**
     * Extract a string variable from POST
     */
    public function get_string()
    {
        $value = $this->get_from_post();

        // perhaps we should check the
    }

    /**
     * Simple wrapper to call woocommerce_form_field
     * @return null
     */
    public function woocommerce_form_field($default)
    {
        woocommerce_form_field( $this->Label, $this->Desc, $default );
    }

    /**
     * Handle error message
     * @return null
     */
    public function error($error_type, $label)
    {
        // Borrow text from WooCommerce and get free translation
        switch ($error_type)
        {
            case "required":
                $this->call_woocommerce_error(__( '%s is a required field.', 'woocommerce' ), $this->Label);
                break;

            case "email":
                $this->call_woocommerce_error(__( '%s is not a valid email address.', 'woocommerce' ), $this->Label);
                break;

            case "phone":
                $this->call_woocommerce_error(__( '%s is not a valid phone number.', 'woocommerce' ), $this->Label);
                break;

            case "postcode":
                $this->call_woocommerce_error(__( 'Please enter a valid postcode / ZIP.', 'woocommerce' ), $this->Label);
                break;

            default:
                $this->call_woocommerce_error(__( 'Invalid value posted for %s', 'woocommerce' ), $this->Label);
                break;
        }
        woocommerce_form_field( $this->Label, $this->Desc, $default );
    }

    /* ------------------------------------------------------------------------------- */
    /**
     * Pull the raw data from POST
     */
    private function get_from_post()
    {
        if (! isset($this->Post[$this->Label]))
        {
            if (this->is_required())
            {
                $this->error('required', $this->Label);
            }
            return null;
        }
        $value = $this->Post[$this->Label];
        $value = sanitize_text_field( $value );
    }

    /**
     * Validate input data
     * @return null, calls $this->error on invalid data
     */
    private function validate_data($data)
    {
        // Code copied from class-wc-form-handler.php

        $validate = $this->get_validate();

		// Validation rules.
		if ( ! empty( $validate ) && is_array( $validate ) )
        {
			foreach ( $validate as $rule )
            {
				switch ( $rule )
                {
					case 'postcode' :
						$data = strtoupper( str_replace( ' ', '', $data ) );

						if ( ! WC_Validation::is_postcode( $data, $_POST[ $load_address . '_country' ] ) )
                        {
							$this->error('postcode', $this->Label);
						}
                        else
                        {
							$data = wc_format_postcode( $data, $_POST[ $load_address . '_country' ] );
						}
						break;

					case 'phone' :
						if ( ! WC_Validation::is_phone( $data ) )
                        {
							$this->error('phone', $this->Label);
						}
						break;

					case 'email' :
						$data = strtolower( $data );

						if ( ! is_email( $data ) )
                        {
							$this->error('email', $this->Label);
						}
						break;
				}
			}
		}

    }

    /**
     * Call the WooCommerce error function
     */
    private function call_woocommerce_error($error_message_format, $label)
    {
        $error_message = sprintf( $error_message_format, $label);

        wc_add_notice( $error_message, 'error' );
    }
}
?>