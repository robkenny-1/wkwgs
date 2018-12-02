<?php
namespace Input\Tests;

/**
 * Routines used to assist in debugging of a RecursiveIterator
 *
 * @author dude
 *
 */
trait T_Debug_RecursiveIterator
{

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function current()
    {
        $val = parent::current();
        return $val;
    }

    public function next(): void
    {
        parent::next();
    }

    public function key()
    {
        $val = parent::key();
        return $val;
    }

    public function valid(): bool
    {
        $val = parent::valid();
        return $val;
    }

    public function rewind(): void
    {
        parent::rewind();
    }

    public function hasChildren(): bool
    {
        // $curr = $this->current();
        $val = parent::hasChildren();
        return $val;
    }

    public function getChildren(): \RecursiveIterator
    {
        // $curr = $this->current();
        $val = parent::getChildren();
        return $val;
    }
}

/**
 * A complete implementation of a RecursiveIterator
 *
 * @author dude
 *
 */
class MyRecursiveIterator implements \RecursiveIterator
{

    protected $data;

    protected $position;

    /**
     * Skills constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = is_array($data) ? $data : [
            $data
        ];
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->data[$this->position];
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
        return $this->position;
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
        return isset($this->data[$this->position]);
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
        return $current instanceof \RecursiveIterator;
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
        if ($this->hasChildren())
        {
            $current = $this->current();
            return $current;
        }
    }
}

class Debug_MyRecursiveIterator extends MyRecursiveIterator
{
    use T_Debug_RecursiveIterator;
}

class Debug_RecursiveArrayIterator extends \RecursiveArrayIterator
{
    use T_Debug_RecursiveIterator;
}

