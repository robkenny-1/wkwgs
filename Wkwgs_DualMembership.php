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

include_once('PluginCore/Wkwgs_LifeCycle.php');
include_once('Wkwgs_Logger.php');

include_once(WP_PLUGIN_DIR . '/weforms/weforms.php' );
include_once(WP_PLUGIN_DIR . '/wkwgs/Input/Field.php' );
include_once(WP_PLUGIN_DIR . '/wkwgs/Input/Factory.php' );


class Wkwgs_DualMembership extends Wkwgs_LifeCycle
{

    /*
     *********************************************************************************
     *  Plugin Values
     *********************************************************************************
     */

    /**
     * Values to display on the product's edit page, under WK&WGS
     * @return array
     */
    protected function get_product_customized()
    {
        return array(
                'wkwgs_is_dual_membership' => array(

                    'admin'             => array(
                        'type'          => 'checkbox',
                        'name'          => 'is_dual_membership',
                        'label'         => __( 'Include Dual email',  Input\DOMAIN ),
                        'required'      => 'yes',
                    ),
                    
                    // Fields to display on product page
                    'display_fields'	=> array(

                        'wkwgs_is_dual_membership_first_name' => array(
                                'type'			=> 'text',
                                'label'			=> __( 'wkwgs first Name', 'wkwgs' ),
                            ),
                        'wkwgs_is_dual_membership_last_name' => array(
                                'type'			=> 'text',
                                'label'			=> __( 'wkwgs last Name', 'wkwgs' ),
                            ),
                        'wkwgs_is_dual_membership_dual_membership_email' => array(
                                'type'			=> 'email',
                                'label'			=> __( 'wkwgs email address', 'wkwgs' ),
                                'required'		=> true,
                                'validate'		=> array( 'email' ), // rules for client side validation
                            ),
                        'wkwgs_is_dual_membership_dual_membership_phone' => array(
                                'type'			=> 'tel',
                                'label'			=> __( 'wkwgs phone', 'wkwgs' ),
                                'validate'		=> array( 'phone' ),
                            ),
                    )
                ),
        );
    }

    protected function getMainPluginFileName()
    {
		// Main page lives in same directory as plugin code
        return 'Wkwgs.php';
    }

    public function getPluginDisplayName()
    {
        //return 'wkwgs';
        return 'Washington Koi & Water Garden Society';
    }
    
    public function addActionsAndFilters()
    {
        // Customer
        add_action( 'woocommerce_before_add_to_cart_button',			array( $this, 'product_show_customized' ) );
        add_filter( 'woocommerce_add_cart_item_data',					array( $this, 'product_save_customized' ), 10, 4 );
        add_filter( 'woocommerce_get_item_data',                        array( $this, 'product_cart_display_customized' ), 10, 2 );
        add_filter( 'woocommerce_add_to_cart_validation',               array( $this, 'product_add_to_cart_validation' ), 10, 3 );
        
        // Editor/Administrator
        add_filter( 'woocommerce_product_data_tabs',					array( $this, 'product_admin_add_tab' ), 10, 1 );
        add_action( 'woocommerce_product_data_panels',					array( $this, 'product_admin_show_customized_checkboxes' ) );
        add_action( 'woocommerce_process_product_meta',					array( $this, 'product_admin_save_customized_checkboxes' ) );
        add_action('admin_menu',                                        array(&$this, 'addSettingsSubMenuPage'));
    }
    
    /*
     *********************************************************************************
     *  Code for Customer
     *********************************************************************************
     */

    /**
     * Helper routine to get list of attributes to display for the $product
     * @return array of product_meta definitions
     */
    private function get_enabled_customized( $product )
    {
        $enabled = array();

        $fields = $this->get_product_customized();

        foreach ( $fields as $key => $value )
        {
            $is_meta_enabled = $product->get_meta( $key );

            if ($is_meta_enabled == 1)
            {
                $enabled[$key] = $value;
            }
        }
        return $enabled;
    }

    /**
     * Display the product's custom fields on product page
     * @return null
     */
    public function product_show_customized()
    {
        $this->Logger()->log_function( 'product_show_customized');

        global $product;

        $enabled = $this->get_enabled_customized( $product );

        if ( empty($enabled) )
        {
            return;
        }
/*
        $form_id = '4220';
        $atts = array();
        $form = weforms()->container['form']->get( $form_id );
        $form_fields   = $form->get_fields();
        $form_settings = $form->get_settings();

        ?>
        <script type="text/javascript">
            if ( typeof wpuf_conditional_items === 'undefined' ) {
                window.wpuf_conditional_items = [];
            }

            if ( typeof wpuf_plupload_items === 'undefined' ) {
                window.wpuf_plupload_items = [];
            }

            if ( typeof wpuf_map_items === 'undefined' ) {
                window.wpuf_map_items = [];
            }
        </script>
        <ul class="wpuf-form form-label-<?php echo $form_settings['label_position']; ?>">
        <?php
        weforms()->fields->render_fields( $form_fields, $form->id, $atts );
        ?>
        <ul class="wpuf-form form-label-<?php echo $form_settings['label_position']; ?>">
        <?php
*/

        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <div class="options_group">
                <?php
                foreach ( $enabled as $product_meta => $product_meta_args )
                {
                    $display_fields = $product_meta_args['display_fields'];
        
                    $this->Logger()->log( 'debug', 'adding custom fields' );
                    foreach ( $display_fields as $field => $field_args )
        
                    {
                        $this->Logger()->log_var( '$field', $field );
                        $this->Logger()->log_var( '$field_args', $field_args );
        
                        woocommerce_form_field(
                            $field,
                            $field_args);
                    }
                }
                ?>
            </div>
        </div>
        <?php

    }

    public function product_add_to_cart_validation( $passed, $product_id, $quantity ) 
    {
        $this->Logger()->log_function( 'product_add_to_cart_validation');
        $this->Logger()->log_var( '$passed', $passed );
        $this->Logger()->log_var( '$product_id', $product_id );
        $this->Logger()->log_var( '$quantity', $quantity );
        $this->Logger()->log_var( '$_POST', $_POST );

        // Verify fields are correct, return false to prevent adding to the cart
        //if (! $valid )
        //{
        //    wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some custom text.', 'kia-plugin-textdomain' ), $product->get_title() ), 'error' );
        //    return false;
        //}

        $this->Logger()->log_var( 'return $passed', $passed );
        return $passed;
    }

    /**
     * Save the product's custom field values when added to the cart
     * @return null
     */
    public function product_save_customized( $cart_item_data, $product_id, $variation_id, $quantity )
    {
        $this->Logger()->log_function( 'product_save_customized');
        $this->Logger()->log_var( '$cart_item_data', $cart_item_data );
        $this->Logger()->log_var( '$product_id', $product_id );
        $this->Logger()->log_var( '$variation_id', $variation_id );
        $this->Logger()->log_var( '$quantity', $quantity );
        $this->Logger()->log_var( '$_POST', $_POST );
 
 	    $product = wc_get_product( $variation_id ? $variation_id : $product_id );
        //$this->Logger()->log_var( '$product', $product );

        $custom = $this->get_enabled_customized( $product );
        //$this->Logger()->log_var( '$custom', $custom );

        if ( ! empty($custom) )
        {
		    $data = $this->get_form_data( $custom, $_POST );
            $this->Logger()->log_var( '$data', $data );

            foreach ($data as $key => $value )
            {
                $cart_item_data[ $key ] = $value;
            }
        }
        
        $this->Logger()->log_var( 'return $cart_item_data', $cart_item_data );
        return $cart_item_data;
    }

    /**
     * product_cart_display_customized
     */
    public function product_cart_display_customized( $item_data, $cart_item )
    {
        $this->Logger()->log_function( 'product_cart_display_customized');
        $this->Logger()->log_var( '$item_data', $item_data );
        $this->Logger()->log_var( '$cart_item', $cart_item );

        if ( empty( $cart_item['wkwgs_is_dual_membership_dual_membership_email'] ) ) {
            return $item_data;
        }
 
        $item_data[] = array(
            'key'     => __( 'Dual Member', 'wkwgs' ),
            'value'   => wc_clean( $cart_item['wkwgs_is_dual_membership_dual_membership_email'] ),
            'display' => '',
        );
 
        $this->Logger()->log_var( '$item_data', $item_data );
        return $item_data;    
    }

    /*
     *********************************************************************************
     *  Code for Manager/Admin
     *********************************************************************************
     */

    /**
        * Create the custom tab
        * @see     https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
        * @param   $tabs
        * @since   1.0.0
        */
    public function product_admin_add_tab( $tabs )
    {
        $tabs['wkwgs'] = array(
            'label'         => __( 'WK & WGS', 'wkwgs' ),	// The name of your panel
            'target'        => 'wkwgs_product_panel',		// Will be used to create an anchor link so needs to be unique
            'class'         => array( 'wkwgs_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type
            'priority'      => 80, // Where your panel will appear. By default, 70 is last item
        );
        return $tabs;
    }

    /**
    * Add the enable button for custom fields
    * @return null
    */
    public function product_admin_show_customized_checkboxes()
    {
        global $post;
        $fields = $this->get_product_customized();
        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <?php
            /*
            // WooCommerce implementation
            foreach ( $fields as $key => $field_args )
            {
                $product = wc_get_product($post);
                $db_value = $product->get_meta($key, true);

                ?><div class="options_group"><?php
                woocommerce_form_field(
                    $key,
                    $field_args,
                    $db_value);
                ?></div><?php
            }
            */
            ?>

            <?php
            // Our implementation
            foreach ( $fields as $key => $field_args )
            {
                $admin      = $field_args[ 'admin' ];

                $product = wc_get_product($post);
                $db_value = $product->get_meta($key, true);
                $checked = $db_value === 'yes' ? 'yes' : 'no';

                $admin[ 'name' ] = $key;
                $admin[ 'value' ] = $checked;

                $checkbox = Input\Factory::Get( $admin );
                $checkbox->print_html( '0' );
            }
            ?>

        </div>

        <?php
    }

    /**
     * extract POST data, perform data cleansing
     */
    private function extract_data_from_post($type, $key, $post)
    {
        //$this->Logger()->log_function( 'extract_data_from_post' );
        //$this->Logger()->log_var( '$type', $type );
        //$this->Logger()->log_var( '$key', $key );
        //$this->Logger()->log_var( '$post', $post );

        $value = '';

		switch ( $type )
        {
			case 'checkbox':
				$value = isset( $post[ $key ] ) ? 1 : 0; // WPCS: input var ok, CSRF ok.
				break;
			
            case 'multiselect':
				$value = isset( $post[ $key ] ) ? implode( ', ', wc_clean( wp_unslash( $post[ $key ] ) ) ) : ''; // WPCS: input var ok, CSRF ok.
				break;
			
            case 'textarea':
				$value = isset( $post[ $key ] ) ? wc_sanitize_textarea( wp_unslash( $post[ $key ] ) ) : ''; // WPCS: input var ok, CSRF ok.
				break;
			
            case 'password':
				$value = isset( $post[ $key ] ) ? wp_unslash( $post[ $key ] ) : ''; // WPCS: input var ok, CSRF ok, sanitization ok.
				break;

            case 'raw':
				$value = $post[ $key ];
				break;

            case 'text':
			default:
				$value = isset( $post[ $key ] ) ? wc_clean( $post[ $key ] ) : ''; // WPCS: input var ok, CSRF ok.
				break;
		}
    
        //$this->Logger()->log_var( '$value', $value );
        return $value;
    }
 
    private function get_form_data( $form, $post )
    {
        $this->Logger()->log_function( 'get_form_data' );
        $this->Logger()->log_var( '$form', $form );
        $this->Logger()->log_var( '$post', $post );

        $data = null;
            
		foreach ( $form as $key => $field )
        {
            $this->Logger()->log_var( '$key', $key );

			$type = sanitize_title( isset( $field['type'] ) ? $field['type'] : 'text' );

            // Get sanitized data from post
            $value = $this->extract_data_from_post($type, $key, $post);
            $this->Logger()->log_var( '$value', $value );

			// Required fields
			if ( ! empty( $field['required'] ) && empty( $value ) )
            {
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $field['label'] ), 'error' );
			}

            // Validate
			if ( ! empty( $value ) )
            {
				// Validation rules.
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'postcode' :
								$post[ $key ] = strtoupper( str_replace( ' ', '', $post[ $key ] ) );

								if ( ! WC_Validation::is_postcode( $post[ $key ], $post[ $load_address . '_country' ] ) ) {
									wc_add_notice( __( 'Please enter a valid postcode / ZIP.', 'woocommerce' ), 'error' );
								} else {
									$post[ $key ] = wc_format_postcode( $post[ $key ], $post[ $load_address . '_country' ] );
								}
								break;
							case 'phone' :
								if ( ! WC_Validation::is_phone( $post[ $key ] ) ) {
									wc_add_notice( sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
							case 'email' :
								$post[ $key ] = strtolower( $post[ $key ] );

								if ( ! is_email( $post[ $key ] ) ) {
									wc_add_notice( sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
						}
					}
				}
			}

            $data[ $key ] = $value;
        }

        $this->Logger()->log_var( 'return data', $data );
        return $data;
    }

    /**
        * Save the enable button for custom fields
        * @param $post_id
        * @since 1.0.0
        */
    public function product_admin_save_customized_checkboxes( $post_id )
    {
        $this->Logger()->log_function( 'product_admin_save_customized_checkboxes' );
        $this->Logger()->log_var( '$post_id', $post_id );

		$data = $this->get_form_data( $this->get_product_customized(), $_POST );

        $product = wc_get_product( $post_id );

        foreach ($data as $key => $value )
        {
            $this->Logger()->log_var( '$key', $value );

            $product->update_meta_data( $key, $value );
        }

        $product->save();
    }

    /*
     *********************************************************************************
     *  Plugin Management
     *********************************************************************************
     */

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData()
    {
        //  http://plugin.michael-simpson.com/?page_id=31
        //
        //  These are global options
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'enable_debug'   => array(__('Enable Debug', 'wkwgs'), 'false', 'true'),
        );
    }

    protected function initOptions()
    {
        $options = $this->getOptionMetaData();
        if (!empty($options))
        {
            foreach ($options as $key => $arr)
            {
                if (is_array($arr) && count($arr > 1))
                {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }
    
    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables()
    {
        //global $wpdb;
        //$tableName = $this->prefixTableName('Options');
        //$wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //    `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables()
    {
        //global $wpdb;
        //$tableName = $this->prefixTableName('Options');
        //$wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }

    protected function Logger()
    {
        static $Logger = null;
        if ( $Logger == null )
        {
            $Logger = new Wkwgs_Logger();
        }

        return $Logger;
    }
}
