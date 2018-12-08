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

use Exception;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once (__DIR__ . '/Input.php');
include_once (__DIR__ . '/Tests/Debug_RecursiveIterator.php');

/* ------------------------------------------------------------------------- */
/* Manage a collection of IHtmlPrinters */
/* ------------------------------------------------------------------------- */
class ElementList implements \IteratorAggregate, IHtmlPrinter, IHtmlPrinterList
{

    public function __construct($children)
    {
        // Note: We do not construct our parent iterator here
        //
        if (gettype($children) === 'string')
        {
            $children = [
                new HtmlText($children)
            ];
        }
        $this->set_children($children);
    }

    public function test_get_children(): array
    {
        return $this->children;
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html(): string
    {
        $html = '';

        foreach ($this->children as $child)
        {

            $child_html = $child->get_html();

            $html .= $child_html;
        }

        return $html;
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlPrinterList routines */
    /* --------------------------------------------------------------------- */
    protected $children = [];

    /**
     * Replace all children with the new values
     *
     * @return null
     */
    public function set_children(?array $children): void
    {
        $this->children = [];

        if ($children instanceof IHtmlPrinterList)
        {
            foreach ($children as $child)
            {
                $this->add_child($child);
            }
        }
        else if (gettype($children) === 'array')
        {
            foreach ($children as $child)
            {
                $this->add_child($child);
            }
        }
        else if (! empty($children))
        {
            $this->add_child($children);
        }
    }

    /**
     * Append a single content item
     *
     * @return null
     */
    public function add_child(IHtmlPrinter $child): void
    {
        if (! is_null($child))
        {
            // Automatically convert strings to a HtmlText()
            if (gettype($child) === 'string')
            {
                throw new Exception(__METHOD__ . 'passed a string for $child');
            }

            // Only allow IHtmlPrinter as children
            if ($child instanceof IHtmlPrinter)
            {
                $this->children[] = $child;
            }
            else
            {
                throw new \Exception(__METHOD__ . 'Attempting to add non IhtmlPrinter');
            }
        }
        else
        {
            throw new \Exception(__METHOD__ . '$child is null');
        }
    }

    /**
     * Get a RecursiveIteratorIterator
     *
     * @param
     *            RecursiveIteratorIterator A RecursiveIteratorIterator
     */
    public function get_RecursiveIteratorIterator(int $mode = \RecursiveIteratorIterator::SELF_FIRST): \RecursiveIteratorIterator
    {
        $it = $this->getIterator();
        $reval = new \RecursiveIteratorIterator($it, $mode);

        return $reval;
    }

    /* --------------------------------------------------------------------- */
    /* \IteratorAggregate routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get an iterator for this object
     *
     * {@inheritdoc}
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator(): \ArrayIterator
    {
        $retval = new RecursiveArrayObjectIterator($this->children);
        return $retval;
    }
}

