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
        // $logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args() );
        // $logger->log_var( '$this', $this );
        $html = '';

        foreach ($this->children as $child)
        {
            // $logger->log_var( '$child', $child );

            $child_html = $child->get_html();
            // $logger->log_var( '$child_html', $child_html );

            $html .= $child_html;
        }

        // $logger->log_return( $html );
        return $html;
    }

    /* ------------------------------------------------------------------------- */
    /* IHtmlPrinterList routines */
    /* ------------------------------------------------------------------------- */
    protected $children = [];

    /**
     * Replace all children with the new values
     *
     * @return null
     */
    public function set_children(?array $children): void
    {
        // $logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args() );
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

        // $logger->log_return( $this->children );
    }

    /**
     * Append a single content item
     *
     * @return null
     */
    public function add_child(IHtmlPrinter $child): void
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        if (! is_null($child))
        {
            // Automatically convert strings to a HtmlText()
            if (gettype($child) === 'string')
            {
                throw new Exception(__METHOD__ . 'passed a string for $child');
                // $logger->log_msg('Converting to HtmlText()');
                // $child = new HtmlText($child);
            }

            // Only allow IHtmlPrinter as children
            if ($child instanceof IHtmlPrinter)
            {
                // $logger->log_msg('adding $child');
                $this->children[] = $child;
            }
            else
            {
                // $logger->log_msg('$child is not IHtmlPrinter');
                throw new \Exception(__METHOD__ . 'Attempting to add non IhtmlPrinter');
            }
        }
        else
        {
            // $logger->log_msg('$child is null');
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

    /* ------------------------------------------------------------------------- */
    /* \IteratorAggregate routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Get an iterator for this object
     *
     * {@inheritdoc}
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator(): \ArrayIterator
    {
        $retval = new RecursiveArrayObjectIterator($this->children);
        return $retval;
    }
}

/* ------------------------------------------------------------------------- */
/* Special types of IHtmlPrinter */
/* ------------------------------------------------------------------------- */
class HtmlSnippet implements IHtmlPrinter
{

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function get_html(): string
    {
        return $this->text;
    }
}

class HtmlText extends HtmlSnippet
{

    protected $text;

    public function __construct(string $text)
    {
        parent::__construct(htmlspecialchars($text));
    }
}

class Callback implements IHtmlPrinter
{

    protected $callback = '';

    protected $params = null;

    public function __construct(callable $callback, ?array $params = null)
    {
        // $logger = new \Wkwgs_Function_Logger(__METHOD__, func_get_args());
        $this->callback = $callback;
        $this->params = $params;
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
        // $logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args() );
        $html = '';

        if (! empty($this->params))
        {
            $html .= call_user_func_array($this->callback, $this->params);
        }
        else
        {
            $html .= call_user_func($this->callback);
        }

        // $logger->log_return( $html );
        return $html;
    }
}
