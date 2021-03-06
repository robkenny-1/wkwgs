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

class Form extends Element implements IHtmlInput
{
    const Tag_Type = 'form';
    const Attributes_Default = [
        'name' => 'form0',
        'action' => '#', // submit data to same page
        'method' => 'post',
        'enctype' => 'multipart/form-data',
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

    /**
     * Return either the GET or POST data, depending on the form submission
     *
     * @return array of posted data
     */
    public function get_submit_data(): array
    {
        $submit = [];

        $form_name = $this->get_name();
        $submit_method = $this->get_attribute('method');
        switch ($submit_method)
        {
            case 'post':
                $submit = $_POST;
                break;

            case 'get':
                $submit = $_GET;
                break;

            default:
                $msg = "Form '$form_name': Unknown submit method: '$submit_method'";
                throw new \Exception($msg);
        }

        return $submit;
    }

    public function has_duplicate_names(): bool
    {
        $is_duplicate = FALSE;

        $existing_names = [];

        foreach ($this->get_RecursiveIteratorIterator() as $child)
        {
            // Only IHtmlInput need to have unique names
            // except for buttons
            if ($child instanceof IHtmlInput && ! $child->is_button())
            {
                $name = $child->get_name();

                if (isset($existing_names[$name]))
                {
                    $is_duplicate = TRUE;
                    break;
                }

                $existing_names[$name] = TRUE;
            }
        }

        return $is_duplicate;
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
    /* --------------------------------------------------------------------- */
    public function define_attribute_secondary(): array
    {
        $parent = parent::define_attribute_secondary();
        return array_merge($parent, self::Attributes_Secondary);
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlInput routines */
    /* --------------------------------------------------------------------- */

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
        // Intentionally do nothing
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
        $validation_errors = [];

        $name = $this->get_name();

        if (empty($post))
        {
            $validation_errors[] = new HtmlValidateError('$post is empty', $name,
                $this);
        }
        else if ($this->has_duplicate_names())
        {
            $validation_errors[] = new HtmlValidateError(
                'input objects do not all have unique names', $name, $this);
        }
        else
        {
            foreach ($this->get_RecursiveIteratorIterator() as $child)
            {
                if ($child instanceof IHtmlInput)
                {
                    $errors = $child->validate($post);
                    if (! empty($errors))
                    {
                        $validation_errors = array_merge($validation_errors,
                            $errors);
                    }
                }
            }
        }

        return $validation_errors;
    }

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value(array $post)
    {
        $values = [];

        if (! empty($post))
        {
            foreach ($this->get_RecursiveIteratorIterator() as $child)
            {
                if ($child instanceof IHtmlInput)
                {
                    $name = $child->get_name();
                    if (! empty($name))
                    {
                        $value = $child->get_value($post);
                        if (isset($value))
                        {
                            $values[$name] = $value;
                        }
                    }
                }
            }
        }

        return $values;
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
        $this->set_attribute('form', $form_id);
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */
    public function validate_post(string $name, array $post): array
    {
        $validation_errors = [];

        // Perform data validation

        return $validation_errors;
    }
}

?>