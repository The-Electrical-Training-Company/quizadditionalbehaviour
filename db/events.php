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
 * Events definition for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => '\local_quizadditionalbehaviour\event\observer::quiz_attempt_submitted',
        // Run this after db transaction has been committed successfully.
        'internal' => false,
    ],
    [
        'eventname' => '\mod_quiz\event\user_override_created',
        'callback' => '\local_quizadditionalbehaviour\event\observer::user_override_created',
    ],
    [
        'eventname' => '\mod_quiz\event\user_override_updated',
        'callback' => '\local_quizadditionalbehaviour\event\observer::user_override_created',
    ],
    [
        'eventname' => '\mod_quiz\event\group_override_created',
        'callback' => '\local_quizadditionalbehaviour\event\observer::user_override_created',
    ],
    [
        'eventname' => '\mod_quiz\event\group_override_updated',
        'callback' => '\local_quizadditionalbehaviour\event\observer::user_override_created',
    ],
];