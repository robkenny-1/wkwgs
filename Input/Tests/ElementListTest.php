<?php
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

define('ABSPATH', '1');

include_once ('..\Input.php');

/* -------------------------------------------------------------------------------- */
function test_iterator(string $name, int $expected, \RecursiveIteratorIterator $iter, int $mode = \RecursiveIteratorIterator::LEAVES_ONLY): void
{
    $count = 0;
    $order = [];
    foreach ($iter as $it)
    {
        $order[] = $it;
        $count += 1;
    }

    if ($count !== $expected)
    {
        throw new Exception('test "' . $name . '" failed. Expected ' . $expected . " got $count");
    }
}

function test_ElementList_iterator(string $name, int $expected, $data, int $mode = \RecursiveIteratorIterator::SELF_FIRST): void
{
    if (is_array($data))
    {
        $data = new \Input\Element([
            'tag' => '$name',
            'attributes' => [],
            'contents' => $data,
        ]);
    }
    $it = $data->get_RecursiveIteratorIterator($mode);

    test_iterator($name, $expected, $it, $mode);
}
/* -------------------------------------------------------------------------------- */
// Test iteration

$h1 = new \Input\Element([
    'tag' => 'h1',
    'attributes' => [],
    'contents' => [],
]);

$h2 = new \Input\Element([
    'tag' => 'h2',
    'attributes' => [],
    'contents' => [],
]);

$h3 = new \Input\Element([
    'tag' => 'h3',
    'attributes' => [],
    'contents' => [],
]);

$h4 = new \Input\Element([
    'tag' => 'h4',
    'attributes' => [],
    'contents' => [],
]);

$h5 = new \Input\Element([
    'tag' => 'h5',
    'attributes' => [],
    'contents' => [],
]);

$h6 = new \Input\Element([
    'tag' => 'h6',
    'attributes' => [],
    'contents' => [],
]);

$h7 = new \Input\Element([
    'tag' => 'h7',
    'attributes' => [],
    'contents' => [],
]);

test_ElementList_iterator('zero elements', 0, []);
test_ElementList_iterator('array of one element', 1, [
    $h1
]);

test_ElementList_iterator('two elements', 2, [
    $h1,
    $h2
]);

/*
 * @formatter:off
 *
 * Hierarchy:
 * |-h1
 * | +--h2
 * |-h3
 *   +-h4
 *     +-h5
 *     |-h6
 *       +-h7
 *
 * @formatter:off
 */
$h1->add_child($h2);
$h1->add_child($h3);
$h3->add_child($h4);
$h4->add_child($h5);
$h4->add_child($h6);
$h6->add_child($h7);

// $h1 has a total of 6 children
test_ElementList_iterator('heirarchy', 6, $h1);

// LEAF_ONLY returns zero, as none of our elements are considered to be a leaf
test_ElementList_iterator('hierarchy, LEAVES_ONLY', 0, $h1, \RecursiveIteratorIterator::LEAVES_ONLY);

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

test_ElementList_iterator('get_cart_form', 7, $cf);
test_ElementList_iterator('get_cart_form', 7, $cf, \RecursiveIteratorIterator::SELF_FIRST);
test_ElementList_iterator('get_cart_form', 1, $cf, \RecursiveIteratorIterator::LEAVES_ONLY);
test_ElementList_iterator('get_cart_form', 7, $cf, \RecursiveIteratorIterator::CHILD_FIRST);

/* -------------------------------------------------------------------------------- */

echo 'test complete';
