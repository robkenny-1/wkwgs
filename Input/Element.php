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
namespace Input;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once (__DIR__ . '/Input.php');

/* ------------------------------------------------------------------------- */
/* Classes */
/* ------------------------------------------------------------------------- */

/*
 * class should implement IAttribute,
 * current get_attributes() should be renamed get_attributes_handler()
 * similar for IHtmlPrinterList
 */
class Element extends ElementList implements IHtmlElement
{

    // string
    protected $tag;

    // [ string ]
    protected $attributes;

    // ElementList
    public function __construct($desc)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        if (gettype($desc) === 'string')
        {
            $tag = $desc;
            $attributes = [];
            $children = [];
        }
        else
        {
            $tag = $desc['tag'] ?? '';
            $attributes = $desc['attributes'] ?? [];
            $children = $desc['contents'] ?? [];
        }

        if (empty($tag))
        {
            $msg = '$tag is empty';
            // $logger->log_msg($msg);
            throw new \Exception($msg);
        }

        $this->tag = $tag;
        $default = $this->define_attribute_default();
        $compound = $this->define_attribute_secondary();
        $this->attributes = new Attributes($attributes, $default, $compound);
        parent::__construct($children);
    }

    /* ------------------------------------------------------------------------- */
    /* IHtmlElement routines */
    /* ------------------------------------------------------------------------- */
    public function get_tag(): string
    {
        return $this->tag;
    }

    /* ------------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html(): string
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        $tag = $this->tag;
        $tag = htmlspecialchars($tag);
        // $logger->log_var('tag', $this->tag);

        $html = '';

        $is_void = Helper::is_void_element($this->tag);

        $html .= "<$tag";
        $html .= $this->attributes->get_html();
        $html .= '>';
        if (! $is_void)
        {
            $html .= parent::get_html();
            $html .= "</$tag>";
        }

        // $logger->log_return($html);
        return $html;
    }

    /* ------------------------------------------------------------------------- */
    /* IAttribute routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Set the attributes of both types
     *
     * @param array $attributes
     *            associative array of attribute name/value
     * @return null
     */
    public function set_attributes(array $attributes, array $default = [])
    {
        $this->attributes->set_attributes($attributes, $default);
    }

    /**
     * Get the attributes that are not in $compound
     *
     * @return array, current attributes
     */
    public function get_attributes(): array
    {
        return $this->attributes->get_attributes();
    }

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute(string $attribute)
    {
        return $this->attributes->get_attribute($attribute);
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute(string $attribute, $value)
    {
        $this->attributes->set_attribute($attribute, $value);
    }

    /* ------------------------------------------------------------------------- */
    /* IAttributeSecondary routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Define attributes that belong to compound elements
     *
     * @param array $compound
     *            non-associative array of attribute names
     *            that exist for the compound elements
     * @return null
     */
    public function set_attributes_secondary(array $compound)
    {
        $this->attributes->set_attributes_secondary($compound);
    }

    /**
     * Get the list of defined secondary attributes
     *
     * @return array
     */
    public function get_attributes_secondary_names(): array
    {
        return $this->attributes->get_attributes_secondary_names();
    }

    /**
     * Get the attributes that are in $compound
     *
     * @return array indexed array of the alternate values
     */
    public function get_attributes_secondary(): array
    {
        return $this->attributes->get_attributes_secondary();
    }

    /**
     * Get the value of a single alternate attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute_secondary(string $attribute)
    {
        return $this->attributes->get_attribute_secondary($attribute);
    }

    /* ------------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* ------------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        return [];
    }

    /* ------------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /* ------------------------------------------------------------------------- */
    public function define_attribute_secondary(): array
    {
        return [];
    }

    /* ------------------------------------------------------------------------- */
    /* Helper routines for HTML */
    /* ------------------------------------------------------------------------- */

    /**
     * Echo this objects HTML string to output
     *
     * @return null
     */
    public function render()
    {
        $html = $this->get_html();
        echo $html;
    }
}

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

    /* ------------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* ------------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        return self::Attributes_Default;
    }

    /* ------------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /* ------------------------------------------------------------------------- */
    public function define_attribute_secondary(): array
    {
        return self::Attributes_Secondary;
    }

    /* ------------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* ------------------------------------------------------------------------- */

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
        $container_tag = Attributes::get_attribute_and_remove('tag', $container_attributes);
        $label_text = Attributes::get_attribute_and_remove('text', $label_attributes);

        $label_contents = [];

        if (! empty($label_text))
        {
            $required = $this->get_attribute('required');

            $label_contents[] = new HtmlText($label_text);

            if (Helper::is_true($required))
            {
                $label_contents[] = new Element([
                    'tag' => 'abbr',
                    'attributes' => [
                        'class' => 'required',
                        'title' => 'required',
                    ],
                    'contents' => [
                        new HtmlSnippet('&nbsp;*')
                    ],
                ]);
            }
        }

        $compound = new Element([
            'tag' => $container_tag,
            'attributes' => $container_attributes,
            'contents' => [
                new Element([
                    'tag' => 'label',
                    'attributes' => $label_attributes,
                    'contents' => $label_contents
                ]),
                new \Input\Callback([
                    $this,
                    'get_html_core'
                ])
            ]
        ]);

        $html = $compound->get_html();

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
        return $this->get_attribute('name');
    }

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate(array $post): array
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        $name = $this->get_name();
        $raw = $post[$name] ?? '';
        $required = Helper::is_true($this->get_attribute('required'));
        $pattern = $this->get_attribute('pattern');

        $validation_errors = [];

        // These first three errors preclude all others
        if (empty($name))
        {
            $validation_errors[] = new HtmlValidateError('$name is empty', $name, $this);
        }
        else if (empty($post))
        {
            $validation_errors[] = new HtmlValidateError('$post is empty', $name, $this);
        }
        else if ($required && empty($raw))
        {
            $validation_errors[] = new HtmlValidateError('$post missing required data', $name, $this);
        }
        else
        {
            if (! empty($pattern) && ! empty($raw))
            {
                $delim = '#';
                $pattern = $delim . addcslashes($pattern, $delim) . $delim;

                if (preg_match($pattern, $raw) !== 1)
                {
                    // $logger->log_msg('$raw does not match pattern');

                    $validation_errors[] = new HtmlValidateError('value does not match defined pattern', $name, $this);
                }
            }

            $ve = $this->validate_post($name, $post);
            $validation_errors = array_merge($validation_errors, $ve);
        }

        // $logger->log_return($validation_errors);
        return $validation_errors;
    }

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value(array $post)
    {
        // $logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args(), __CLASS__ );
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

    /* ------------------------------------------------------------------------- */
    /* IHtmlInput routines */
    /* ------------------------------------------------------------------------- */

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
        $this->attributes->set_attribute('form', $form_id);
    }

    /* ------------------------------------------------------------------------- */
    /* InputElement routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    abstract public function cleanse_data($raw);

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
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
