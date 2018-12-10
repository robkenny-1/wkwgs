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

/* ------------------------------------------------------------------------- */
/* Classes */
/* ------------------------------------------------------------------------- */

/*
 * The InputElement Class
 *
 * Layout of the input element
 * <div class="container-class">
 * <label class="label-class">
 * Label Text
 * <input type='text' />
 * </label>
 * </div>
 *
 */
abstract class InputElement extends Element implements IHtmlInput
{
    const Attributes_Default = [
        'type' => 'text',
        'container-tag' => 'div',
    ];
    const Attributes_Secondary = [
        'label-',
        'container-',
    ];

    public function is_button(): bool
    {
        $tag = $this->get_tag();
        if ($tag === 'button')
        {
            return TRUE;
        }

        $type = $this->get_type();
        if ($tag === 'input' && $type === 'button')
        {
            return TRUE;
        }

        return FALSE;
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        return self::Attributes_Default;
    }

    /* ---------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /* ---------------------------------------------------------------------- */
    public function define_attribute_secondary(): array
    {
        return self::Attributes_Secondary;
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get the HTML that represents the current Attributes
     *
     * @formatter:off
     *
     * Layout of the output field
     * <div class="container-class">
     *     <label class="css-label">
     *         Label Text
     *         <input class="class" />
     *     </label>
     * </div>
     *
     * <div class="container-class">
     *     <label class="css-label" id=>
     *         Label Text
     *         <input class="class" name="input_name" id="unique_id" />
     *     </label>
     * </div>
     *
     * @formatter:on
     * @return string
     */
    public function get_html(): string
    {
        $this->enforce_id_attributes();

        $label_attributes = $this->get_attributes_secondary()['label'];
        $container_attributes = $this->get_attributes_secondary()['container'];
        $container_tag = Attributes::get_attribute_and_remove('tag',
            $container_attributes);
        $label_text = Attributes::get_attribute_and_remove('text',
            $label_attributes);

        $label_contents = [];

        if (! empty($label_text))
        {
            $required = $this->get_attribute('required');

            $label_contents[] = new HtmlText($label_text);

            if (Helper::is_true($required))
            {
                $label_contents[] = new Element(
                    [
                        'tag' => 'abbr',
                        'attributes' => [
                            'class' => 'required',
                            'title' => 'required'
                        ],
                        'contents' => [
                            new HtmlSnippet('&nbsp;*')
                        ]
                    ]);
            }
        }

        $compound = new Element(
            [
                'tag' => $container_tag,
                'attributes' => $container_attributes,
                'contents' => [
                    new Element(
                        [
                            'tag' => 'label',
                            'attributes' => $label_attributes,
                            'contents' => $label_contents
                        ]),
                    new \Wkwgs\Input\Callback([
                        $this,
                        'get_html_core'
                    ])
                ]
            ]);

        $html = $compound->get_html();

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
        return $this->get_attribute('type');
    }

    /**
     * Get the name of the HTML input element,
     * this is the index used to retrieve the data from POST
     *
     * @return string name of the input element
     */
    public function get_name(): string
    {
        return $this->get_attribute('name');
    }

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value(array $post)
    {
        $cleansed = null;
        $name = $this->get_name();

        if (isset($post[$name]) && empty($this->validate($post)))
        {
            $cleansed = $this->cleanse_data($post[$name]);
        }

        return $cleansed;
    }

    /**
     * Set the contents of the input element
     * Some input elements, such as the checkbox, do not store their current
     * contents in the value attribute.
     * This routine, given the value returned
     * by get_value(), sets the appropriate attribute.
     *
     * @param mixed $value
     *            New value of the input element
     */
    public function set_value($value)
    {
        $this->set_attribute('value', $value);
    }

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side
     * validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate(array $post): array
    {
        $name = $this->get_name();
        $raw = $post[$name] ?? '';
        $required = Helper::is_true($this->get_attribute('required'));
        $pattern = $this->get_attribute('pattern');

        $validation_errors = [];

        // These first three errors preclude all others
        if (empty($name))
        {
            $validation_errors[] = new HtmlValidateError('$name is empty', $name,
                $this);
        }
        else if (empty($post))
        {
            $validation_errors[] = new HtmlValidateError('$post is empty', $name,
                $this);
        }
        else if ($required && empty($raw))
        {
            $validation_errors[] = new HtmlValidateError(
                '$post missing required data', $name, $this);
        }
        else
        {
            if (! empty($pattern) && ! empty($raw))
            {
                $delim = '#';
                $pattern = $delim . addcslashes($pattern, $delim) . $delim;

                if (preg_match($pattern, $raw) !== 1)
                {

                    $validation_errors[] = new HtmlValidateError(
                        'value does not match defined pattern', $name, $this);
                }
            }

            $maxlength = $this->get_attribute('maxlength');
            if (! empty($maxlength) && strlen($raw) > $maxlength)
            {
                $validation_errors[] = new HtmlValidateError(
                    'value exceeds maximum length', $name, $this);
            }

            $ve = $this->validate_post($name, $post);
            $validation_errors = array_merge($validation_errors, $ve);
        }

        return $validation_errors;
    }

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id(): string
    {
        return $this->get_attribute('form');
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id(string $form_id)
    {
        $this->attributes->set_attribute('form', $form_id);
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    abstract public function cleanse_data($raw);

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side
     * validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    abstract public function validate_post(string $name, array $post): array;

    /**
     * Get the HTML for the core (<input>) object
     * This is used by get_html
     *
     * @return string HTML of the <input> element
     */
    public function get_html_core(): string
    {
        return parent::get_html();
    }

    /**
     * Ensure the label's 'for' attribute references the input's 'id' attribute
     * Will throw if input does not have name
     * If input 'id' is unset, will use 'name'
     * Enforces label 'for' attribute to match input 'id'
     *
     * @throws \Exception
     */
    protected function enforce_id_attributes(): void
    {
        $name = $this->get_attribute('name');
        if (empty($name))
        {
            $msg = __METHOD__ . ': input object does not have name attribute';
            throw new \Exception($msg);
        }

        // If id is missing, we can use the name
        $id = $this->get_attribute('id');
        if (empty($id))
        {
            // name is only required to be unique within the form,
            // we can make it unique if we prefix it with the form's 'id'
            $form_id = $this->get_form_id();
            if (empty($form_id))
            {
                $id = $name;
            }
            else
            {
                $id = $form_id . '-' . $name;
            }

            $this->set_attribute('id', $id);
        }

        $label_for = $this->get_attribute('label-for');
        if ($label_for !== $id)
        {
            $this->set_attribute('label-for', $id);
        }
    }
}
