<?php

/*
 * Input Copyright (C) 2018 Rob Kenny
 *
 * WordPress Plugin Template is free software: you can redistribute it and/or
 * modify
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

/**
 * The text input Class
 *
 * @since 1.0.0
 */
class Button extends InputElement
{
    const Tag_Type = 'button';
    const Attributes_Default = [
        'type' => 'submit'
    ];
    const Attributes_Secondary = [];

    public function __construct($desc)
    {
        if (gettype($desc) !== 'array')
        {
            return;
        }

        $desc['tag'] = self::Tag_Type;
        parent::__construct($desc);
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        $parent = parent::define_attribute_default();
        return array_merge($parent, self::Attributes_Default);
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /*
     * -------------------------------------------------------------------------
     */
    public function define_attribute_secondary(): array
    {
        // We don't use parent's get_html()
        return self::Attributes_Secondary;
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get the HTML that represents the current Attributes
     *
     * Layout of the output field
     * <div class="css-container">
     * <label class="css-label">
     * Label Text
     * <input class="css-input" />
     * </label>
     * </div>
     *
     * @return string
     */
    public function get_html(): string
    {
        $remaining = $this->get_attributes();
        $label = $this->get_attribute('label-text');

        // Buttons can contain (inline only) child elements
        // Use label-text if none were specifiede

        $children = ! empty($this->children) ? $this->children : [
            new HtmlText($label)
        ];

        $button = new Element(
            [
                'tag' => 'button',
                'attributes' => $remaining,
                'contents' => $children
            ]);
        $html = $button->get_html();

        return $html;
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlInput routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get the type of Input
     *
     * @return string Input type
     */
    public function get_type(): string
    {
        return 'button';
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */

    /**
     * Validate data for a button
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post(string $name, array $post): array
    {
        $validation_errors = [];

        // A form may contain mulitple buttons all with the same name
        // therefore we cannot verify that the contents of $post matches
        // our expected value

        return $validation_errors;
    }
}
