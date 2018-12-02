<?php
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

define('ABSPATH', '1');

include_once ('..\Input.php');

/* -------------------------------------------------------------------------------- */
function test_iterator(string $name, int $expected, \RecursiveIteratorIterator $iter, int $mode = \RecursiveIteratorIterator::LEAVES_ONLY)
{
    $count = 0;
    $types = [];
    foreach ($iter as $it)
    {
        $tt = gettype($it);
        if ($tt === 'object')
        {
            $tt = get_class($it);
        }
        $prev = $types[$tt] ?? 0;
        $types[$tt] = $prev + 1;

        $count += 1;
    }

    if ($count !== $expected)
    {
        throw new Exception('test "' . $name . '" failed. Expected ' . $expected . " got $count");
    }
}

function test_Element_iterator(string $name, int $expected, \Input\Element $data, int $mode = \RecursiveIteratorIterator::SELF_FIRST)
{
    $it = $data->get_RecursiveIteratorIterator($mode);
    // $it = $data->getIterator();
    // $it = new \RecursiveIteratorIterator($it, $mode);

    return test_iterator($name, $expected, $it, $mode);
}

function test_ArrayObjectIterator(string $name, int $expected, $data, int $mode = \RecursiveIteratorIterator::SELF_FIRST)
{
    $el = new \Input\ArrayObjectIterator($data);
    $it = new \RecursiveIteratorIterator($el, $mode);

    return test_iterator($name, $expected, $it, $mode);
}
/* -------------------------------------------------------------------------------- */
// Test iteration

$e1 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e2 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e3 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e4 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e5 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e6 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e7 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$e3->add_child($e4);
$e4->add_child($e5);
$e4->add_child($e6);
$e6->add_child($e7);

test_ArrayObjectIterator('zero elements', 0, []);
test_ArrayObjectIterator('array of one element', 1, [
    $e1
]);

test_ArrayObjectIterator('two elements', 2, [
    $e1,
    $e2
]);

test_ArrayObjectIterator('heirarchy', 7, [
    $e1,
    $e2,
    $e3
]);

// LEAF_ONLY returns zero, as none of our elements are considered to be a leaf
test_ArrayObjectIterator('hierarchy, LEAVES_ONLY', 0, [
    $e1,
    $e2,
    $e3
], \RecursiveIteratorIterator::LEAVES_ONLY);

/* -------------------------------------------------------------------------------- */
function get_cart_form(): \Input\Form
{
    $css_aligned_input = 'margin-left:5px';

    $form = new \Input\Form([
        'attributes' => [
            'name' => 'unused in ui',
        ],
    ]);

    $fieldset = new \Input\Element([
        'tag' => 'fieldset',
        'attributes' => [],
        'contents' => [
            new \Input\Element([
                'tag' => 'legend',
                'attributes' => [
                    'style' => '',
                ],
                'contents' => [
                    new \Input\HtmlText('Second Member Registration'),
                ],
            ])
        ],
    ]);
    $form->add_child($fieldset);

    $fieldset->add_child(new \Input\Text([
        'attributes' => [
            'name' => 'wkwgs_dual_membership_first',
            'label-text' => 'First Name',
            'style' => $css_aligned_input,
        ],
    ]));
    $fieldset->add_child(new \Input\Text([
        'attributes' => [
            'name' => 'wkwgs_dual_membership_last',
            'label-text' => 'Last Name',
            'style' => $css_aligned_input,
        ],
    ]));
    $fieldset->add_child(new \Input\Text([
        'attributes' => [
            'name' => 'wkwgs_dual_membership_email',
            'label-text' => 'Email',
            'required' => True,
            'style' => $css_aligned_input,
        ],
    ]));
    $fieldset->add_child(new \Input\Telephone([
        'attributes' => [
            'name' => 'wkwgs_dual_membership_phone',
            'label-text' => 'Phone',
            'style' => $css_aligned_input,
        ],
    ]));

    return $form;
}

$cf = get_cart_form();

test_Element_iterator('get_cart_form', 7, $cf);
test_Element_iterator('get_cart_form', 7, $cf, \RecursiveIteratorIterator::SELF_FIRST);
test_Element_iterator('get_cart_form', 1, $cf, \RecursiveIteratorIterator::LEAVES_ONLY);
test_Element_iterator('get_cart_form', 7, $cf, \RecursiveIteratorIterator::CHILD_FIRST);

/* -------------------------------------------------------------------------------- */

echo 'test complete';
