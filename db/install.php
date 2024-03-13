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
 * Install script for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

function xmldb_local_quizadditionalbehaviour_install() {
    global $DB;
    $dbman = $DB->get_manager();

    // Add fields to the quiz table.
    $table = new xmldb_table('quiz');

    // Define field disablecorrect to be added to quiz.
    $fieldname = 'disablecorrect';
    $previousfield = 'allowofflineattempts';
    $field = new xmldb_field($fieldname, XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', $previousfield);

    // Conditionally launch add field disablecorrect.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field disablecorrectshowcorrect to be added to quiz.
    $fieldname = 'disablecorrect_showcorrect';
    $previousfield = 'disablecorrect';
    $field = new xmldb_field($fieldname, XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', $previousfield);

    // Conditionally launch add field disablecorrectshowcorrect.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field disableshowcorrectforstudent to be added to quiz.
    $fieldname = 'disableshowcorrectforstudent';
    $previousfield = 'disablecorrect_showcorrect';
    $field = new xmldb_field($fieldname, XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', $previousfield);

    // Conditionally launch add field disableshowcorrectforstudent.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field customgrading to be added to quiz.
    $fieldname = 'customgrading';
    $previousfield = 'disableshowcorrectforstudent';
    $field = new xmldb_field($fieldname, XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', $previousfield);

    // Conditionally launch add field customgrading.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
}