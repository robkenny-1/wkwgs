<?php
?>
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

        include_once ('..\Input.php');

        // Wkwgs_Logger::clear();
        // Wkwgs_Logger::$Disable = False;

        // $logger = new \Wkwgs_Function_Logger(__METHOD__, null);

        /**
         * Class Skills
         */
        class Skills implements \RecursiveIterator
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

        class Skills2 extends \RecursiveArrayIterator
        {

            protected $data;

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
                parent::__construct($this->data);
            }
        }

        class Skill
        {

            protected $name;

            public function __construct(string $name)
            {
                $this->name = $name;
            }

            public function get_name(): string
            {
                return $this->name;
            }
        }

        /**
         * Class CollectionsIterator
         */
        class CollectionsIterator
        {

            public static function iterate(RecursiveIterator $iterator)
            {
                foreach ($iterator as $value)
                {
                    if ($iterator->hasChildren())
                    {
                        self::iterate($iterator->getChildren());

                        continue;
                    }
                    echo "<br>$value";
                }
            }

            public static function iterate_r(RecursiveIterator $iterator)
            {
                $rii = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($rii as $value)
                {
                    if ($value instanceof Skill)
                    {
                        echo $value->get_name();
                    }
                    else if ($value instanceof Skills)
                    {
                        echo 'Skipping Skills';
                    }
                    else if (gettype($value) === 'string')
                    {
                        echo $value;
                    }
                    else
                    {
                        echo 'Skipping unknown type: ' . gettype($value);
                    }
                    echo '<br>';
                }
            }
        }

        $skills = new Skills2([
            // Languages
            'javascripts',
            'php',
            'ruby',
            new Skills2([
                // Frameworks
                'jquery',
                'css',
                'angular',
                'react',
                'symfony',
                'zend framework'
            ]),
            new Skills2([
                // OS
                'ubuntu',
                'debian',
                'centos',
                new Skills([
                    // Cloud
                    new Skill('amazon ws'),
                    new Skill('google cloud'),
                    new Skill('microsoft azure'),
                ])
            ])
        ]);

        // CollectionsIterator::iterate($skills);
        echo '<br>';
        CollectionsIterator::iterate_r($skills);
        ?>
        <h1>
            Done
        </h1>
    </body>
</html>
