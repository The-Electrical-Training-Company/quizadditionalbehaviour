<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Webservice functions definitions for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_quizadditionalbehaviour_get_custom_grading' => [
        'classname' => 'local_quizadditionalbehaviour\local\external\custom_grading',
        'methodname' => 'get',
        'description' => 'Get the information to build the custom grading view',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_quizadditionalbehaviour_set_custom_grading' => [
        'classname' => 'local_quizadditionalbehaviour\local\external\custom_grading',
        'methodname' => 'set',
        'description' => 'Sets comment/grade in the database',
        'type' => 'write',
        'ajax' => true,
    ],
];
