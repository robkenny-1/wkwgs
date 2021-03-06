<?php

/*
 * Input Copyright (C) 2018 Rob Kenny
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
namespace Wkwgs\Input;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once ('Input.php');

class Email extends Text
{
    const Input_Type = 'email';
    const Attributes_Default = array(
        'type' => self::Input_Type,
    );

    /* --------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        $parent = parent::define_attribute_default();
        return array_merge($parent, self::Attributes_Default);
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */

    /**
     * Validate data for an email address
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post(string $name, array $post): array
    {
        $ve = [];

        // Perform data validation

        $raw = $post[$name] ?? '';

        if (! filter_var($raw, FILTER_VALIDATE_EMAIL))
        {
            $ve[] = new HtmlValidateError('not a valid email address', $name, $this);
        }

        return $ve;
    }
}
