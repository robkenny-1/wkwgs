<?php
/*
 * "Washington Koi & Water Garden Society" Copyright (C) 2018 Rob Kenny
 *
 * WordPress Plugin Template is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WordPress Plugin Template is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Contact Form to Database Extension.
 * If not, see http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once ('PluginCore/Wkwgs_LifeCycle.php');
include_once ('Wkwgs_Logger.php');

include_once (WP_PLUGIN_DIR . '/wkwgs/Input/Input.php');

class Wkwgs_DualMembership_Nonce implements \Input\IHtmlPrinter, \Input\IHtmlInputValue
{

    protected $nonce;

    protected $action;

    protected $form;

    protected function generate_random_string(): string
    {
        return bin2hex(random_bytes(10)); // 20 chars, only 0-9a-f
    }

    public function __construct(\Input\Form $form)
    {
        $this->form = $form;

        // A nonce provides protection for logged in users only
        if (! is_user_logged_in())
        {
            $this->nonce = '';
            $this->action = '';
        }
        else
        {
            // Use random strings for nonce and action, making it much harder to hack
            $this->nonce = $this->generate_random_string();
            $this->action = $this->generate_random_string();
        }
    }

    /* ------------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* ------------------------------------------------------------------------- */
    public function get_html(): string
    {
        $html = '';

        if (! empty($this->nonce))
        {
            ob_start();
            wp_nonce_field($this->action, $this->nonce);
            $html = ob_get_clean();
        }

        return $html;
    }

    /* ------------------------------------------------------------------------- */
    /* IHtmlInputValue routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Get the name of the HTML input element,
     * this is the index used to retrieve the data from POST
     *
     * @return string name of the input element
     */
    public function get_name(): string
    {
        return $this->nonce;
    }

    /**
     * Set the contents of the input element
     * Some input elements, such as the checkbox, do not store their current
     * contents in the value attribute.
     * This routine, given the value returned by get_value(),
     * sets the appropriate attribute.
     *
     * @param mixed $value
     *            New value of the input element
     */
    public function set_value($value)
    {
        // Not allowed,
    }

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array Validation errors, will be empty if good
     */
    public function validate(array $post): array
    {
        $ve = [];

        if (! empty($this->nonce))
        {
            if (! isset($post[$this->nonce]) || ! wp_verify_nonce($post[$this->nonce], $this->action))
            {
                $ve[] = new \Input\HtmlValidateError('Nonce did not verify', $this->form->get_name(), $this->form);
            }
        }
        return $ve;
    }

    /**
     * Get this object's data in $post
     *
     * @return array,string | string contents of the input object
     */
    public function get_value(array $post)
    {
        // No cleansing necessary for nonce
        if (isset($post[$this->nonce]))
        {
            return $post[$this->nonce];
        }
    }
}

class Wkwgs_DualMembership extends Wkwgs_LifeCycle
{

    protected function get_admin_form(): \Input\Form
    {
        $form = new \Input\Form([
            'attributes' => [
                'name' => 'unused in ui',
            ],
            'contents' => []
        ]);

        $fieldset = new \Input\Element([
            'tag' => 'fieldset',
            'attributes' => [],
            'contents' => [],
        ]);

        $checkbox = new \Input\Element([
            'tag' => 'div',
            'attributes' => [
                'class' => 'options_group',
            ],
            'contents' => [
                new \Input\Checkbox([
                    'attributes' => [
                        'container-tag' => 'p',
                        'container-class' => 'form-field',
                        'label-text' => 'Show Dual Membership',
                        'style' => 'margin-right: 5px !important;',
                        'name' => 'wkwgs_dual_membership_use',
                        'class' => 'checkbox',
                    ],
                ])
            ],
        ]);

        $fieldset->add_child($checkbox);
        $form->add_child($fieldset);

        return $form;
    }

    protected function get_cart_form(): \Input\Form
    {
        // Styles for the input elements
        $fieldset_style = 'border-color: #77a464; border: solid 2px; padding: 2px';
        $legend_style = 'margin-bottom: 0px;';
        $container_style = 'margin: 5px;display: flex;';
        $label_style = 'flex: 2;';
        $style = 'flex: 5;';

        $form = new \Input\Form([
            'attributes' => [
                'name' => 'unused in ui',
            ],
        ]);

        $form->add_child(new Wkwgs_DualMembership_Nonce($form));

        $fieldset = new \Input\Element([
            'tag' => 'fieldset',
            'attributes' => [
                'style' => $fieldset_style,
                'class' => 'bg-info',
            ],
            'contents' => [
                new \Input\Element([
                    'tag' => 'legend',
                    'attributes' => [
                        'style' => $legend_style,
                    ],
                    'contents' => [
                        new \Input\HtmlText('Second Member Registration'),
                    ],
                ])
            ],
        ]);
        $form->add_child($fieldset);

        $div = new \Input\Element([
            'tag' => 'div',
            'attributes' => [],
            'contents' => [
                new \Input\Text([
                    'attributes' => [
                        'name' => 'wkwgs_dual_membership_first',
                        'label-text' => 'First Name',
                        'container-style' => $container_style,
                        'label-style' => $label_style,
                        'style' => $style,
                    ],
                ]),
                new \Input\Text([
                    'attributes' => [
                        'name' => 'wkwgs_dual_membership_last',
                        'label-text' => 'Last Name',
                        'container-style' => $container_style,
                        'label-style' => $label_style,
                        'style' => $style,
                    ],
                ]),
                new \Input\Text([
                    'attributes' => [
                        'name' => 'wkwgs_dual_membership_email',
                        'label-text' => 'Email',
                        'required' => True,
                        'container-style' => $container_style,
                        'label-style' => $label_style,
                        'style' => $style,
                    ],
                ]),
                new \Input\Telephone([
                    'attributes' => [
                        'name' => 'wkwgs_dual_membership_phone',
                        'label-text' => 'Phone',
                        'container-style' => $container_style,
                        'label-style' => $label_style,
                        'style' => $style,
                    ],
                ])
            ],
        ]);

        $fieldset->add_child($div);

        return $form;
    }

    public function set_form_field_values(\WC_Product $product, \Input\Form $form)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        foreach ($form->get_RecursiveIteratorIterator() as $child)
        {
            if ($child instanceof \Input\IHtmlInput)
            {
                $name = $child->get_name();
                $product_meta = $product->get_meta($name, True);
                // $logger->log_var('$name', $name);
                // $logger->log_var('$product_meta', $product_meta);

                if (isset($product_meta))
                {
                    $child->set_value($product_meta);
                }
            }
        }
    }

    /*
     * ********************************************************************************
     * Plugin Values
     * ********************************************************************************
     */
    protected function getMainPluginFileName()
    {
        // Main page lives in same directory as plugin code
        return 'Wkwgs.php';
    }

    public function getPluginDisplayName()
    {
        // return 'wkwgs';
        return 'Washington Koi & Water Garden Society';
    }

    public function addActionsAndFilters()
    {
        // Customer
        add_action('woocommerce_before_add_to_cart_button', array(
            $this,
            'product_cart_show'
        ));
        add_filter('woocommerce_add_cart_item_data', array(
            $this,
            'product_cart_save'
        ), 10, 4);
        add_filter('woocommerce_add_to_cart_validation', array(
            $this,
            'product_cart_validation'
        ), 10, 3);
        add_filter('woocommerce_get_item_data', array(
            $this,
            'product_cart_item_data'
        ), 10, 2);

        // Editor/Administrator
        add_filter('woocommerce_product_data_tabs', array(
            $this,
            'product_admin_tab'
        ), 10, 1);
        add_action('woocommerce_product_data_panels', array(
            $this,
            'product_admin_show'
        ));
        add_action('woocommerce_process_product_meta', array(
            $this,
            'product_admin_save'
        ));
    }

    /**
     * Display the product's custom fields on product page
     *
     * @return void
     */
    public function product_cart_show()
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        global $product;
        ?>
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <div class="options_group">
                <?php
        $form = $this->get_cart_form();
        $this->set_form_field_values($product, $form);

        // Since we do not render the form itself, we just iterate over all it's top-level children
        foreach ($form->getIterator() as $child)
        {
            $html = $child->get_html();
            echo $html;
        }
        ?>
            </div>
        </div>
        <?php
    }

    public function product_cart_validation($passed, $product_id, $quantity)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        $form = $this->get_cart_form();
        $validation_errors = $form->validate($_POST);
        $passed = empty($validation_errors);

        // $logger->log_return($passed);
        return $passed;
    }

    /**
     * Save the product's custom field values when added to the cart
     *
     * @return mixed updated $cart_item_data
     */
    public function product_cart_save($cart_item_data, $product_id, $variation_id, $quantity)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        // $product = wc_get_product($variation_id ? $variation_id : $product_id);
        $form = $this->get_cart_form();
        $form_values = $form->get_value($_POST);
        // Save all the values to the cart
        foreach ($form_values as $key => $value)
        {
            $cart_item_data[$key] = $value;
        }

        // $logger->log_return($cart_item_data);
        return $cart_item_data;
    }

    /**
     * product_cart_item_data
     */
    public function product_cart_item_data($item_data, $cart_item)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        if (empty($cart_item['wkwgs_dual_membership_email']))
        {
            return $item_data;
        }

        $item_data[] = array(
            'key' => __('Dual Member', 'wkwgs'),
            'value' => wc_clean($cart_item['wkwgs_dual_membership_email']),
            'display' => '',
        );

        // $logger->log_return($item_data);
        return $item_data;
    }

    /*
     * ********************************************************************************
     * Code for Manager/Admin
     * ********************************************************************************
     */

    /**
     * Create the custom tab
     *
     * @see https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
     * @param
     *            $tabs
     * @since 1.0.0
     */
    public function product_admin_tab($tabs)
    {
        $tabs['wkwgs'] = array(
            'label' => __('WK & WGS', 'wkwgs'), // The name of your panel
            'target' => 'wkwgs_product_panel', // Will be used to create an anchor link so needs to be unique
            'css-input' => array(
                'wkwgs_tab',
                'show_if_simple',
                'show_if_variable'
            ), // Class for your panel tab - helps hide/show depending on product type
            'priority' => 80, // Where your panel will appear. By default, 70 is last item
        );
        return $tabs;
    }

    /**
     * Add the enable button for custom fields
     *
     * @return void
     */
    public function product_admin_show()
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());

        // global $thepostid, $product_object;
        ?>
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <?php
        // Render the children only
        foreach ($this->get_admin_form() as $field)
        {
            // $logger->log_var('$field', $field);
            $field->render();
        }
        ?>
        </div>
        <?php
    }

    /**
     * Save the enable button for custom fields
     *
     * @param
     *            $post_id
     * @since 1.0.0
     */
    public function product_admin_save($post_id)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        $product = wc_get_product($post_id);

        $form = $this->get_cart_form();
        // Save all the values to the cart
        foreach ($form->get_value($_POST) as $key => $value)
        {
            $product->update_meta_data($key, $value);
        }

        $product->save();
    }

    /*
     * ********************************************************************************
     * Plugin Management
     * ********************************************************************************
     */

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     *
     * @return array of option meta data.
     */
    public function getOptionMetaData()
    {
        // http://plugin.michael-simpson.com/?page_id=31
        //
        // These are global options
        return array(
            // '_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'enable_debug' => array(
                __('Enable Debug', 'wkwgs'),
                'false',
                'true'
            ),
        );
    }

    protected function initOptions()
    {
        $options = $this->getOptionMetaData();
        if (! empty($options))
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
     *
     * @return void
     */
    protected function installDatabaseTables()
    {
        // global $wpdb;
        // $tableName = $this->prefixTableName('Options');
        // $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        // `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     *
     * @return void
     */
    protected function unInstallDatabaseTables()
    {
        // global $wpdb;
        // $tableName = $this->prefixTableName('Options');
        // $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }
}
