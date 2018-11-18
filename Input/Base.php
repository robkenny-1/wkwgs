<?php
/*
    Input Copyright (C) 2018 Rob Kenny

    Input is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Input is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

namespace Input;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once('Input.php');

/*-------------------------------------------------------------------------*/
/* Manage a collection of IHtmlPrinters */
/*-------------------------------------------------------------------------*/

class ElementList implements IHtmlPrinter, IHtmlPrinterList
{
    public function __construct( $children )
    {
        if ( gettype( $children ) === 'string' )
        {
            $children = [ new HtmlText( $children ) ];
        }
        $this->set_children( $children );
    }

    /**
     * Replace all content with the new values
     *
     * @return null
     */
    protected function set_children( array $children )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->children = [];

        if ( $children instanceof IHtmlPrinterList )
        {
            foreach ( $children as $child )
            {
                $this->add_child( $child );
            }
        }
        else if ( gettype( $children ) === 'array' )
        {
            foreach ( $children as $child )
            {
                $this->add_child( $child );
            }
        }
        else
        {
            $this->add_child( $children );
        }

        //$logger->log_return( $this->children );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html() : string
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        //$logger->log_var( '$this', $this );

        $html = '';

        foreach ( $this->children as $child )
        {
            //$logger->log_var( '$child', $child );

            $child_html = $child->get_html();
            //$logger->log_var( '$child_html', $child_html );

            $html .= $child_html;
        }

        //$logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinterList routines */
    /*-------------------------------------------------------------------------*/

    protected $children = [];

    /**
     * Append a single content item
     *
     * @return null
     */
    public function add_child( IHtmlPrinter $child )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( ! is_null( $child ) )
        {
            // Automatically convert strings to a HtmlText()
            if ( gettype( $child ) === 'string' )
            {
                $logger->log_msg( 'Converting to HtmlText()' );
                $child = new HtmlText( $child );
            }

            // Only allow IHtmlPrinter as children
            if ( $child instanceof IHtmlPrinter )
            {
                $logger->log_msg( 'adding $child' );
                $this->children[] = $child;
            }
            else
            {
                $logger->log_msg( '$child is not IHtmlPrinter' );
            }
        }
        else
        {
            $logger->log_msg( '$child is null' );
        }
    }

    /*-------------------------------------------------------------------------*/
    /* \Iterator routines */
    /*-------------------------------------------------------------------------*/

    function rewind()
    {
        return reset($this->children);
    }
    function current()
    {
        return current($this->children);
    }
    function key()
    {
        return key($this->children);
    }
    function next()
    {
        return next($this->children);
    }
    function valid()
    {
        return key($this->children) !== null;
    }
}

/*-------------------------------------------------------------------------*/
/* Special types of IHtmlPrinter */
/*-------------------------------------------------------------------------*/

class HtmlText implements IHtmlPrinter
{
    protected $text;

    public function __construct( $text )
    {
        $this->text = $text;
    }
    public function get_html() : string
    {
        return $this->text;
    }
}

class Callback implements IHtmlPrinter
{
    protected   $callback   = '';
    protected   $params     = null;

    public function __construct( callable $callback, ?array $params = null )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->callback     = $callback;
        $this->params       = $params;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html() : string
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $html = '';

        if ( ! empty( $this->params ) )
        {
            $html .= call_user_func_array( $this->callback, $this->params );
        }
        else
        {
            $html .= call_user_func( $this->callback );
        }

        //$logger->log_return( $html );
        return $html;
    }
}
