<?php
/*
 * "WordPress Plugin Template" Copyright (C) 2018 Michael Simpson (email : michael.d.simpson@gmail.com)
 *
 * This following part of this file is part of WordPress Plugin Template for WordPress.
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
namespace Wkwgs;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once ('PluginCore/Wkwgs_LifeCycle.php');

class Wkwgs_Options extends \Wkwgs\Plugin\Wkwgs_LifeCycle
{

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
            'ATextInput' => array(
                __('Enter in some text', 'wkwgs')
            ),
            'AmAwesome' => array(
                __('I like this awesome plugin', 'wkwgs'),
                'false',
                'true'
            ),
            'CanDoSomething' => array(
                __('Which user role can do something', 'wkwgs'),
                'Administrator',
                'Editor',
                'Author',
                'Contributor',
                'Subscriber',
                'Anyone'
            ),
            'Enable Debug' => array(
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

    public function getPluginDisplayName()
    {
        // return 'wkwgs';
        return 'Washington Koi & Water Garden Society';
    }

    protected function getMainPluginFileName()
    {
        // Main page lives in same directory as plugin code
        return 'Wkwgs.php';
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

    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     *
     * @return void
     */
    public function upgrade()
    {
    }

    public function addActionsAndFilters()
    {
        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(
            &$this,
            'addSettingsSubMenuPage'
        ));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        // if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false)
        // {
        // wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        // wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        // }

        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37

        // Adding scripts & styles to all pages
        // Examples:
        // wp_enqueue_script('jquery');
        // wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        // wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));

        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39

        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41
    }
}
