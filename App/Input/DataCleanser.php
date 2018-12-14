<?php

/*
 * Input Copyright (C) 2018 Rob Kenny
 *
 * Input is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Input is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Contact Form to Database Extension.
 * If not, see http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Wkwgs\Input;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once (__DIR__ . '/Input.php');

interface IDataCleanser
{

    /**
     * Apply cleansing rules to the provided string
     *
     * @param string $raw
     *            String to be cleansed
     * @param string $rules
     *            Cleansing rules to apply, multiple rules are seperated by
     *            [space or comma] are applied in order.
     * @return string Cleansed string
     */
    public function cleanse(string $raw, string $rules): string;

    /**
     * Add a custom rule to the data cleanser
     *
     * @param string $name
     *            Friendly name of rule.
     * @param callable $rule
     *            Callable that accepts a string and returns a string
     */
    public function add_rule(string $name, callable $rule): void;

    /**
     * Get the callable for the named rule
     *
     * @param string $name
     *            Name of rule to retrieve
     * @return callable Function that accepts a single string argument,
     *         returns null if no rule found for $name.
     */
    public function get_rule(string $name): ?callable;
}

final class DataCleanser implements IDataCleanser
{

    /* --------------------------------------------------------------------- */
    /* Singleton routines */
    /* --------------------------------------------------------------------- */

    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance(): DataCleanser
    {
        static $inst = null;
        if ($inst === null)
        {
            $inst = new DataCleanser();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instantiate it
     */
    private function __construct()
    {}

    /* --------------------------------------------------------------------- */
    /* DataCleanser routines */
    /* --------------------------------------------------------------------- */
    protected $mapping = [
        'trim' => 'trim',
        'html' => 'htmlspecialchars',
        'slashes' => 'stripslashes',
        'sql' => 'mysql_real_escape_string'
    ];

    /**
     * Apply cleansing rules to the provided string
     *
     * @param string $raw
     *            String to be cleansed
     * @param string $rules
     *            Cleansing rules to apply, multiple rules are seperated by
     *            [space or comma] are applied in order.
     * @return string Cleansed string
     */
    public function cleanse(string $raw, string $rules): string
    {
        $names = preg_split("/( |,|;)/", $rules);
        foreach ($names as $name)
        {
            $callable = self::get_rule($name);
            if ($callable != null)
            {
                $raw = call_user_func($callable, $raw);
            }
        }

        return $raw;
    }

    /**
     * Add a custom rule to the data cleanser
     *
     * @param string $name
     * @param string $rule
     *            Callable that accepts a string and returns a string
     */
    public function add_rule(string $name, callable $rule): void
    {
        self::$mapping[$name] = $rule;
    }

    /**
     * Add a custom rule to the data cleanser
     *
     * @param string $name
     *            Friendly name of rule.
     * @param callable $rule
     *            Callable that accepts a string and returns a string
     */
    public function get_rule(string $name): ?callable
    {
        if (isset($this->mapping[$name]))
        {
            return $this->mapping[$name];
        }
        return 'DataCleanser::empty_rule';
    }

    private static function empty_rule(string $raw): string
    {
        return $raw;
    }
}
