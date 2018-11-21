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

include_once(WP_PLUGIN_DIR . '/wkwgs/Input/Input.php' );

//Wkwgs_Logger::clear();

class Wkwgs_DualMembership extends Wkwgs_LifeCycle
{
    protected function get_admin_form() : \Input\Form
    {
        $form = new \Input\Form([
            'attributes'        => [
                'name'          => 'unused in ui',
            ],
            'contents'          => [
            ]
        ]);

        $fieldset = new Input\Element([
            'tag'               => 'fieldset',
            'attributes'        => [
            ],
            'contents'          => [
            ],
        ]);

        $checkbox = new Input\Element([
            'tag'               => 'div',
            'attributes'        => [
                'class'         => 'options_group',
            ],
            'contents'          => [
                new \Input\Checkbox([
                    'attributes'            => [
                        'container-tag'     => 'p',
                        'container-class'   => 'form-field',
                        'label-text'        => 'Show Dual Membership',
                        'label-class'       => '',
                        'name'              => 'wkwgs_dual_membership_use',
                        'class'             => 'checkbox',
                    ],
                ])
            ],
        ]);

        $fieldset->add_child( $checkbox );
        $form->add_child( $fieldset );

        return $form;
    }

    protected function get_cart_form() : \Input\Form
    {
        $form = new Input\Form([
            'attributes'        => [
                'name'          => 'unused in ui',
            ],
        ]);

        $form->add_child(
            new Input\Text( [
                'attributes'    => [
                    'name'          => 'wkwgs_dual_membership_first',
                    'label'         => 'First Name',
                ],
            ])
        );
        $form->add_child(
            new Input\Text( [
                'attributes'    => [
                    'name'          => 'wkwgs_dual_membership_last',
                    'label'         => 'Last Name',
                ],
            ])
        );
        $form->add_child(
            new Input\Email( [
                'attributes'    => [
                    'name'          => 'wkwgs_dual_membership_email',
                    'label'         => 'Email',
                    'required'      => True,
                ],
            ])
        );
        $form->add_child(
            new Input\Phone( [
                'attributes'    => [
                    'name'          => 'wkwgs_dual_membership_phone',
                    'label'         => 'Phone',
                ],
            ])
        );

        return $form;
    }

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

    /**
     * Display the product's custom fields on product page
     * @return void
     */
    public function product_cart_show()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        global $product;

        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <div class="options_group">
            <?php
            // Render the children only
            foreach ( $this->get_cart_form() as $field )
            {
                $field->render();
            }
            ?>
            </div>
        </div>
        <?php
    }

    public function product_cart_validation( $passed, $product_id, $quantity ) 
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $form = $this->get_cart_form();
        $validation_errors = $form->validate( $_POST );

        $logger->log_return( $passed );
        return $passed;
    }

    /**
     * Save the product's custom field values when added to the cart
     * @return updated $cart_item_data
     */
    public function product_cart_save( $cart_item_data, $product_id, $variation_id, $quantity )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
 
 	    $product = wc_get_product( $variation_id ? $variation_id : $product_id );

        $form = $this->get_cart_form();
        // Save all the values to the cart
        foreach ($form->get_value( $_POST ) as $key => $value )
        {
            $cart_item_data[ $key ] = $value;
        }
        
        $logger->log_return( $cart_item_data );
        return $cart_item_data;
    }

    /**
     * product_cart_item_data
     */
    public function product_cart_item_data( $item_data, $cart_item )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( empty( $cart_item['wkwgs_dual_membership_email'] ) )
        {
            return $item_data;
        }
 
        $item_data[] = array(
            'key'     => __( 'Dual Member', 'wkwgs' ),
            'value'   => wc_clean( $cart_item['wkwgs_dual_membership_email'] ),
            'display' => '',
        );
 
        $logger->log_return( $item_data );
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
            'css-input'         => array( 'wkwgs_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type
            'priority'      => 80, // Where your panel will appear. By default, 70 is last item
        );
        return $tabs;
    }

    /**
      * Add the enable button for custom fields
      * @return void
      */
    public function product_admin_show( )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        global $thepostid, $product_object;

        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
        <?php
        // Render the children only
        foreach ( $this->get_admin_form() as $field )
        {
            $logger->log_var( '$field', $field );
            $field->render();
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
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $product = wc_get_product( $post_id );

        $form = $this->get_cart_form();
        // Save all the values to the cart
        foreach ($form->get_value( $_POST ) as $key => $value )
        {
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
}
