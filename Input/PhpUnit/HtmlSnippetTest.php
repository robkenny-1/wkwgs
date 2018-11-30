<?php
include_once '..\..\vendor\Autoload.php';

/**
 * HtmlSnippet test case.
 */
class HtmlSnippetTest extends PHPUnit\Framework\TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->htmlSnippet = null;

        parent::tearDown();
    }

    /**
     *
     * @dataProvider TestData
     */
    public function test_get_html($text, $expected)
    {
        $test = new \Input\HtmlSnippet($text);
        $actual = $test->htmlSnippet->get_html();
        $this->assertSame($expected, $actual);
    }

    public function TestData()
    {
        return [
            [
                null,
                '',
            ],
            [
                '',
                '',
            ],
            [
                ' ',
                ' ',
            ],
            [
                'plain text',
                'plain text',
            ],
            [
                '<h1>element</h1>',
                '<h1>element</h1>',
            ],
        ];
    }
}

