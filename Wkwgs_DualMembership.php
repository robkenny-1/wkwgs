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
                        array(
                            'type'          => 'checkbox',
                            'name'          => 'wkwgs_is_dual_membership',
                            'label'         => __( 'Include Dual email',  Input\DOMAIN ),
                            )
                    ),
                    
                    // Fields to display on product's cart page
                    'cart'	=> array(

                        array(
                                'name'          => 'wkwgs_is_dual_membership_first_name',
                                'type'			=> 'text',
                                'label'			=> __( 'First Name', 'wkwgs' ),
                            ),
                        array(
                                'name'          => 'wkwgs_is_dual_membership_last_name',
                                'type'			=> 'text',
                                'label'			=> __( 'Last Name', 'wkwgs' ),
                            ),
                        array(
                                'name'          => 'wkwgs_is_dual_membership_dual_membership_email',
                                'type'			=> 'email',
                                'label'			=> __( 'Email address', 'wkwgs' ),
                                'required'		=> true,
                            ),
                        array(
                                'name'          => 'wkwgs_is_dual_membership_dual_membership_phone',
                                'type'			=> 'tel',
                                'label'			=> __( 'Phone', 'wkwgs' ),
                            ),
                    )
                ),
        );
    }

    protected function get_form( $form_type, $form_name, $form_desc )
    {
        //\Wkwgs_Logger::log_function( 'get_form' );
        //\Wkwgs_Logger::log_var( '$form_type', $form_type );
        //\Wkwgs_Logger::log_var( '$form_name', $form_name );
        //\Wkwgs_Logger::log_var( '$form_desc', $form_desc );

        if ( 
            empty( $form_type )
            ||
            empty( $form_name )
            ||
            ! isset( $form_desc[ $form_type ] )
            )
        {
            return;
        }

        $form = \Input\Factory::Get(
            array(
            'type'          => 'form',
            'name'          => $form_name . '_' . $form_type . '_panel',
            )
        );

        $fields = $form_desc[ $form_type ];
        foreach ( $fields as $field )
        {
            $form->add_field( \Input\Factory::Get( $field ) );
        }

        //\Wkwgs_Logger::log_var( 'return $form', $form );
        return $form;
    }

    public function set_form_field_values( $product, $form )
    {
        \Wkwgs_Logger::log_function( 'set_form_field_values' );
        foreach ( $form->get_fields() as $field )
        {
            $product_meta = $product->get_meta( $field->get_name(), True );
            \Wkwgs_Logger::log_var( '$product_meta', $product_meta );
            if ( isset( $product_meta ) )
            {
                $field->set_attributes( array( 'value' => $product_meta ) );
            }
        }
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
        add_action( 'woocommerce_before_add_to_cart_button',			array( $this, 'product_cart_show' ) );
        add_filter( 'woocommerce_add_cart_item_data',					array( $this, 'product_cart_save' ), 10, 4 );
        add_filter( 'woocommerce_add_to_cart_validation',               array( $this, 'product_cart_validation' ), 10, 3 );
        add_filter( 'woocommerce_get_item_data',                        array( $this, 'product_cart_item_data' ), 10, 2 );
        
        // Editor/Administrator
        add_filter( 'woocommerce_product_data_tabs',					array( $this, 'product_admin_tab' ), 10, 1 );
        add_action( 'woocommerce_product_data_panels',					array( $this, 'product_admin_show' ) );
        add_action( 'woocommerce_process_product_meta',					array( $this, 'product_admin_save' ) );
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

            if ( \Input\Field::is_true( $is_meta_enabled ) )
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
    public function product_cart_show()
    {
        \Wkwgs_Logger::log_function( 'product_cart_show');

        global $product;

        $enabled = $this->get_enabled_customized( $product );

        if ( empty($enabled) )
        {
            return;
        }

        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <div class="options_group">
                <?php
                foreach ( $enabled as $option_name => $option_items )
                {
                    $form = $this->get_form( 'cart', $option_name, $option_items );
                    $this->set_form_field_values( $product, $form );

                    $form->render_fields();
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function product_cart_validation( $passed, $product_id, $quantity ) 
    {
        \Wkwgs_Logger::log_function( 'product_cart_validation');
        \Wkwgs_Logger::log_var( '$passed', $passed );
        \Wkwgs_Logger::log_var( '$product_id', $product_id );
        \Wkwgs_Logger::log_var( '$quantity', $quantity );
        \Wkwgs_Logger::log_var( '$_POST', $_POST );

        // Verify fields are correct, return false to prevent adding to the cart
        //if (! $valid )
        //{
        //    wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some custom text.', 'kia-plugin-textdomain' ), $product->get_title() ), 'error' );
        //    return false;
        //}

        \Wkwgs_Logger::log_var( 'return $passed', $passed );
        return $passed;
    }

    /**
     * Save the product's custom field values when added to the cart
     * @return null
     */
    public function product_cart_save( $cart_item_data, $product_id, $variation_id, $quantity )
    {
        \Wkwgs_Logger::log_function( 'product_cart_save');
        \Wkwgs_Logger::log_var( '$cart_item_data', $cart_item_data );
        \Wkwgs_Logger::log_var( '$product_id', $product_id );
        \Wkwgs_Logger::log_var( '$variation_id', $variation_id );
        \Wkwgs_Logger::log_var( '$quantity', $quantity );
        \Wkwgs_Logger::log_var( '$_POST', $_POST );
 
 	    $product = wc_get_product( $variation_id ? $variation_id : $product_id );
        //\Wkwgs_Logger::log_var( '$product', $product );

        $enabled = $this->get_enabled_customized( $product );
        //\Wkwgs_Logger::log_var( '$custom', $custom );

        if ( ! empty($enabled) )
        {
            // Loop over all enabled product options
            foreach ( $enabled as $option_name => $option_items )
            {
                // Get the option's values from the cart form
                $form = $this->get_form( 'cart', $option_name, $option_items );
                $form_values = $form->get_values( $_POST );

                // Save all the values to the cart
                foreach ($form_values as $key => $value )
                {
                    $cart_item_data[ $key ] = $value;
                }
            }
        }
        
        \Wkwgs_Logger::log_var( 'return $cart_item_data', $cart_item_data );
        return $cart_item_data;
    }

    /**
     * product_cart_item_data
     */
    public function product_cart_item_data( $item_data, $cart_item )
    {
        \Wkwgs_Logger::log_function( 'product_cart_item_data');
        \Wkwgs_Logger::log_var( '$item_data', $item_data );
        //\Wkwgs_Logger::log_var( '$cart_item', $cart_item );

        if ( empty( $cart_item['wkwgs_is_dual_membership_dual_membership_email'] ) )
        {
            return $item_data;
        }
 
        $item_data[] = array(
            'key'     => __( 'Dual Member', 'wkwgs' ),
            'value'   => wc_clean( $cart_item['wkwgs_is_dual_membership_dual_membership_email'] ),
            'display' => '',
        );
 
        \Wkwgs_Logger::log_var( '$item_data', $item_data );
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
    public function product_admin_tab( $tabs )
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
    public function product_admin_show( )
    {
        global $thepostid, $product_object;

        //\Wkwgs_Logger::log_function( 'product_admin_show' );
        //\Wkwgs_Logger::log_var( '$product_object', $product_object );
        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <?php
            foreach ( $this->get_product_customized() as $option_name => $option_items )
            {
                $form = $this->get_form( 'admin', $option_name, $option_items );
                $this->set_form_field_values( $product_object, $form );

                $form->render_fields();
            }
            ?>
        </div>
        <?php
    }

    /**
        * Save the enable button for custom fields
        * @param $post_id
        * @since 1.0.0
        */
    public function product_admin_save( $post_id )
    {
        \Wkwgs_Logger::log_function( 'product_admin_save' );
        \Wkwgs_Logger::log_var( '$post_id', $post_id );

        $product = wc_get_product( $post_id );

        foreach ( $this->get_product_customized() as $option_name => $option_items )
        {
            $form = $this->get_form( 'admin', $option_name, $option_items );
            $form_values = $form->get_values( $_POST );
            
            foreach ($form_values as $key => $value )
            {
                \Wkwgs_Logger::log_var( '$key', $value );

                $product->update_meta_data( $key, $value );
            }
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
}
