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

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once (__DIR__ . '/Constants.php');
include_once (__DIR__ . '/../Wkwgs_Logger.php');

include_once (__DIR__ . '/Helper.php');
include_once (__DIR__ . '/ArrayObjectIterator.php');
include_once (__DIR__ . '/Interface.php');
include_once (__DIR__ . '/Base.php');
include_once (__DIR__ . '/Attributes.php');
include_once (__DIR__ . '/HtmlValidateError.php');

// Input types
include_once (__DIR__ . '/Element.php');
include_once (__DIR__ . '/Form.php');
include_once (__DIR__ . '/Text.php');
include_once (__DIR__ . '/Button.php');
include_once (__DIR__ . '/Checkbox.php');
include_once (__DIR__ . '/RadioButton.php');
include_once (__DIR__ . '/Email.php');
include_once (__DIR__ . '/Telephone.php');
