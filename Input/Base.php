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
include_once (__DIR__ . '/Tests/Debug_RecursiveIterator.php');

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
        $html = '';

        if (! empty($this->params))
        {
            $html .= call_user_func_array($this->callback, $this->params);
        }
        else
        {
            $html .= call_user_func($this->callback);
        }

        return $html;
    }
}
