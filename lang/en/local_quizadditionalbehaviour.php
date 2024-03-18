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
 * English language strings for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

// Default langstring.
$string['pluginname'] = 'Quiz additional behaviour';
$string['additionalquestionbehaviour'] = 'Additional question behaviour';

// Settings things.
$string['customgrading'] = 'Quiz custom grading';
$string['customgrading_help'] = 'Turns on quiz custom grading';

$string['disablealreadycorrectquestions']                   = 'Prevent users from answering correctly answered questions from their previous attempt';
$string['disablealreadycorrectquestions_help']              = 'Enabling this setting will prevent users from answering any questions they have already answered correctly in their previous attempt';

$string['disablealreadycorrectquestions_showcorrect']       = 'Show correct answer with disabled correct';
$string['disablealreadycorrectquestions_showcorrect_help']  = 'Enabling this setting will show the users the correct answer for the question that was previously answered correctly';

$string['disableshowcorrectforstudent']                     = 'Disable "Whether correct" for students';
$string['disableshowcorrectforstudent_help']                = 'Prevents students from seeing specific question marks. For example the ticks for each choice in a multiple choice question';

$string['disableshowcorrectforall']                         = 'Disable "Whether correct" for all users';
$string['disableshowcorrectforall_help']                    = 'Prevents all users from seeing specific question marks. For example the ticks for each choice in a multiple choice question';

// Question state strings.
$string['previouslycompleted']                              = 'Previously Completed';
$string['alreadyansweredcorrectly']                         = 'Already answered correctly';

// Other strings.
$string['manualgradecomment'] = 'Correct in previous attempt';
$string['customgradingerror'] = 'There was an error in your input, please try again.';

// Capability strings.
$string['quizadditionalbehaviour:ignorerestrictions']       = 'Ignore restrictions on viewing specific question marks.';