<?php ?>
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <h1 id="logo">
            Stub Tests
        </h1>

        <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT);

        define('ABSPATH', '1');

        include_once( '..\Input.php' );

        Wkwgs_Logger::clear();
        Wkwgs_Logger::$Disable = False;
        $logger                = new \Wkwgs_Function_Logger(__METHOD__, null);

        class TT implements RecursiveIterator
        {
            protected $name;
            protected $var = [];
            protected $position;

            public function __construct(string $name)
            {
                $this->name = $name;
            }

            public function add(TT $tt)
            {
                $this->var[] = $tt;
            }

            public function get_name(): string
            {
                return $this->name;
            }

            /**
             * Return the current element
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
             * @link http://php.net/manual/en/iterator.next.php
             * @return void Any returned value is ignored.
             * @since 5.0.0
             */
            public function next()
            {
                ++$this->position;
            }

            /**
             * Return the key of the current element
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
             * @link http://php.net/manual/en/iterator.valid.php
             * @return boolean The return value will be casted to boolean and then evaluated.
             * Returns true on success or false on failure.
             * @since 5.0.0
             */
            public function valid()
            {
                return isset($this->data[$this->position]);
            }

            /**
             * Rewind the Iterator to the first element
             * @link http://php.net/manual/en/iterator.rewind.php
             * @return void Any returned value is ignored.
             * @since 5.0.0
             */
            public function rewind()
            {
                $this->position = 0;
            }

            /**
             * Returns if an iterator can be created for the current entry.
             * @link http://php.net/manual/en/recursiveiterator.haschildren.php
             * @return bool true if the current entry can be iterated over, otherwise returns false.
             * @since 5.1.0
             */
            public function hasChildren()
            {
                $current = $this->current();
                return is_countable($current);
            }

            /**
             * Returns an iterator for the current entry.
             * @link http://php.net/manual/en/recursiveiterator.getchildren.php
             * @return RecursiveIterator An iterator for the current entry.
             * @since 5.1.0
             */
            public function getChildren()
            {
                if ($this->hasChildren())
                {
                    $current = $this->current();
                    return new self($current);
                }
            }
        }

        $t1 = new TT('t1');
        $t2 = new TT('t2');
        $t3 = new TT('t3');
        $t1->add($t2);
        $t2->add(new TT('t2-1'));
        $t2->add($t3);
        $t3->add(new TT('t3-1'));
        $t3->add(new TT('t3-2'));
        $t3->add(new TT('t3-3'));

        echo '<h3>RecursiveIteratorIterator</h3>';
        $rii = new \RecursiveIteratorIterator($t1, \RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($rii as $value)
        {
            echo "<br>$value";
        }

        /*
          $path = 'C:\xampp\htdocs\WordPress\wp-content\plugins\wkwgs\Input';
          $dir  = new DirectoryIterator($path);
          echo '<h3>DirectoryIterator</h3>';
          echo "[$path]<br>";
          foreach ($dir as $file)
          {
          echo " ├ $file<br>";
          }

          $files = new IteratorIterator($dir);
          echo '<h3>IteratorIterator</h3>';
          echo "[$path]<br>";
          foreach ($files as $file)
          {
          echo " ├ $file<br>";
          }

          $dir = new RecursiveDirectoryIterator($path);
          echo '<h3>RecursiveDirectoryIterator</h3>';
          echo "[$path]<br>";
          foreach ($dir as $file)
          {
          echo " ├ $file<br>";
          }

          $files = new RecursiveIteratorIterator($dir);
          echo '<h3>RecursiveIteratorIterator</h3>';
          echo "[$path]<br>";
          foreach ($files as $file)
          {
          echo " ├ $file<br>";
          }

          echo '<h3>Nicely Formatted Directory Listing</h3>';
          $unicodeTreePrefix = function(RecursiveTreeIterator $tree) {
          $prefixParts = [
          RecursiveTreeIterator::PREFIX_LEFT         => ' ',
          RecursiveTreeIterator::PREFIX_MID_HAS_NEXT => '│ ',
          RecursiveTreeIterator::PREFIX_END_HAS_NEXT => '├ ',
          RecursiveTreeIterator::PREFIX_END_LAST     => '└ '
          ];
          foreach ($prefixParts as $part => $string)
          {
          $tree->setPrefixPart($part, $string);
          }
          };
          $dir  = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_FILENAME | RecursiveDirectoryIterator::SKIP_DOTS);
          $tree = new RecursiveTreeIterator($dir);
          $unicodeTreePrefix($tree);

          echo "[$path]<br>";
          foreach ($tree as $filename => $line)
          {
          echo $tree->getPrefix(), $filename, "<br>";
          }
         */
        ?>
        <h1>
            Done
        </h1>
    </body>
</html>
