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

class MyArrayIterator implements \Iterator
{

    protected $data;

    protected $position;

    /**
     * ArrayObjectIterator constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /* ------------------------------------------------------------------------- */
    /* \Iterator routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $retval = $this->data[$this->position];
        return $retval;
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        ++ $this->position;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        $retval = $this->position;
        return $retval;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *         Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        $retval = isset($this->data[$this->position]);
        return $retval;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
}

/**
 * A problem with ArrayIterator is that it treats all objects
 * as having children.
 * Objects with out children as treated as a leaf.
 *
 * @author dude
 *
 */
class ArrayObjectIterator extends MyArrayIterator implements \RecursiveIterator
{

    /* ------------------------------------------------------------------------- */
    /* \RecursiveIterator routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Returns if an iterator can be created for the current entry.
     *
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren(): bool
    {
        $current = $this->current();
        $retval = is_iterable($current);
        return $retval;
    }

    /**
     * Returns an iterator for the current entry.
     *
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return \RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren(): \RecursiveIterator
    {
        if (! $this->hasChildren())
        {
            throw new \Exception(__METHOD__ . ' object does not hasChildren()');
        }
        $current = $this->current();
        if ($current instanceof \Traversable)
        {
            $retval = $current->getIterator();
            return $retval;
        }
        $retval = new self($current);
        return $retval;
    }
}