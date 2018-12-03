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

/**
 * Routines used to assist in debugging of a RecursiveIterator
 * This trait will store the traversal commands in the $traversal variable
 *
 * @author dude
 *
 */
trait T_Debug_RecursiveIterator
{

    protected $traversal = [];

    public function __construct($data)
    {
        $this->traversal[] = [
            '__construct',
            $data
        ];
        parent::__construct($data);
    }

    public function current()
    {
        $val = parent::current();
        $this->traversal[] = [
            'current',
            $val
        ];
        return $val;
    }

    public function next(): void
    {
        $this->traversal[] = [
            'next'
        ];
        parent::next();
    }

    public function key()
    {
        $val = parent::key();
        $this->traversal[] = [
            'key',
            $val
        ];
        return $val;
    }

    public function valid(): bool
    {
        $val = parent::valid();
        $this->traversal[] = [
            'valid',
            $val
        ];
        return $val;
    }

    public function rewind(): void
    {
        $this->traversal[] = [
            'rewind'
        ];
        parent::rewind();
    }

    public function hasChildren(): bool
    {
        // $curr = $this->current();
        $val = parent::hasChildren();
        $this->traversal[] = [
            'hasChildren',
            $val
        ];
        return $val;
    }

    public function getChildren(): \RecursiveIterator
    {
        // $curr = $this->current();
        $val = parent::getChildren();
        $this->traversal[] = [
            'getChildren',
            $val
        ];
        return $val;
    }
}

/**
 * A full implementation of an Iterator.
 * Basically a reimplemtation of ArrayIterator, used to understance how Iterator works
 */
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
 * A problem with RecursiveArrayIterator is that it treats all objects as having children.
 *
 * @author dude
 *
 */
class RecursiveArrayObjectIterator extends \RecursiveArrayIterator
{

    /* ------------------------------------------------------------------------- */
    /* \RecursiveArrayIterator routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Returns if an iterator can be created for the current entry.
     * RecursiveArrayIterator will return True for all objects, regardless if they implement \Traversable,
     * this implementation only returns True if the current object is actually Traversable
     *
     * @return bool true if the current entry can be iterated over, otherwise returns false.
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
     * @return \RecursiveIterator An iterator for the current entry.
     */
    public function getChildren(): \RecursiveIterator
    {
        $current = $this->current();
        $retval = $current->getIterator();
        return $retval;
    }
}

class RecursiveArrayObjectIterator_Debug extends RecursiveArrayObjectIterator
{
    use T_Debug_RecursiveIterator;
}
