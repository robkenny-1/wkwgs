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
namespace Wkwgs\Input\Test;

class TestFramework
{
    protected $falsify_callback;
    protected $form;

    public function __construct(callable $falsify_callback)
    {
        $this->falsify_callback = $falsify_callback;
    }

    public function test_form(\Wkwgs\Input\Form $form)
    {
        $this->form = $form;

        $this->add_submit_buttons();

        $post = $this->form->get_submit_data();
        $this->handle_post($post);

        // Display the form and all input objects
        $this->form->render();

        $this->session_display();
    }

    /* --------------------------------------------------------------------- */
    /* Class routines */
    /* --------------------------------------------------------------------- */
    public function handle_post(array $post)
    {
        if (! empty($post))
        {

            $this->session_erase();

            switch ($post['submit'])
            {
                case 'clear':
                    // post data has already been erased
                    break;

                case 'mock':
                    $this->session_mock($post);
                // fall through

                case 'submit':
                default:
                    $this->session_validate($post);
                    break;
            }
        }
    }

    public function session_display()
    {
        if (isset($_SESSION['post_orig']))
        {
            echo '<h2>$_POST (Original)</h2>';
            print_r($_SESSION['post_orig']);
        }

        if (isset($_SESSION['post']))
        {
            echo '<h2>$_POST</h2>';
            print_r($_SESSION['post']);
        }

        // Print out any validation errors stored in the session
        if (isset($_SESSION['form_errors']))
        {

            echo '<div>';
            echo '<h1>Validation Errors</h1>';

            $errors = unserialize($_SESSION['form_errors']);
            foreach ($errors as $error)
            {
                if ($error instanceof \Wkwgs\Input\IHtmlValidateError)
                {
                    $error_html = htmlspecialchars($error->get_error());
                    $name_html = htmlspecialchars($error->get_name());
                    echo "<b>Error Object:</b> $name_html => $error_html</br>";

                    if ($error->get_name() == '$name is empty')
                    {
                        print_r($error->get_object());
                    }
                }
                else
                {
                    echo '<p>Ignore error<br>';
                    print_r($error);
                    echo '<br></p>';
                }
            }
            echo '</div>';
        }

        // Print out any results stored in the session
        if (isset($_SESSION['form_values']))
        {

            echo '<div>';
            echo '<h1>Submit Values</h1>';

            $values = $_SESSION['form_values'];
            foreach ($values as $name => $value)
            {
                echo "<h4 style='margin: 0px;padding: 0px;'>&lt;input name='$name'&gt;</h4><pre style='margin: 0px;padding: 0px;'>|$value|</pre><br>";
            }
            echo '</div>';
        }
    }

    protected function add_submit_buttons()
    {
        $button_name = 'submit';

        $submit = new \Wkwgs\Input\Button(
            [
                'attributes' => [
                    'type' => 'submit',
                    'name' => $button_name,
                    'value' => 'submit',
                    'label-text' => 'Submit',
                ],
            ]);

        $mock = new \Wkwgs\Input\Button(
            [
                'attributes' => [
                    'type' => 'submit',
                    'name' => $button_name,
                    'value' => 'mock',
                    'label-text' => 'Use mock POST data',
                ],
            ]);

        $clear = new \Wkwgs\Input\Button(
            [
                'attributes' => [
                    'type' => 'submit',
                    'name' => $button_name,
                    'value' => 'clear',
                    'label-text' => 'Clear Session',
                ],
            ]);

        $this->form->add_child(
            new \Wkwgs\Input\Element(
                [
                    'tag' => 'span',
                    'contents' => [
                        new \Wkwgs\Input\HtmlText('Form Buttons'),
                        $submit,
                        $mock,
                        $clear
                    ]
                ]));
    }

    protected function session_erase()
    {
        unset($_SESSION['post']);
        unset($_SESSION['post_orig']);
        unset($_SESSION['form_values']);
        unset($_SESSION['form_errors']);
    }

    protected function session_mock(array & $post)
    {
        $_SESSION['post_orig'] = $post;

        $post = falsify_post($post);
    }

    protected function session_validate(array $post)
    {
        $_SESSION['post'] = $post;

        // Validate data and store results
        $errors = $this->form->validate($post);
        if (! empty($errors))
        {
            $_SESSION['form_errors'] = serialize($errors);
        }

        // Extract data and store results
        $form_values = $this->form->get_value($post);
        $_SESSION['form_values'] = $form_values;

        // Redirect
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    protected function post_falsify(array & $post)
    {
        call_user_func_array($this->falsify_callback, [
            & $post
        ]);
    }
}