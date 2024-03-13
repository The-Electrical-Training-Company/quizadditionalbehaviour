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
 * Lib file for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

function local_quizadditionalbehaviour_coursemodule_standard_elements(\moodleform $formwrapper, \MoodleQuickForm $mform): void {
    if (!$formwrapper instanceof mod_quiz_mod_form) {
        // Not a quiz.
        return;
    }
    $quizconfig = get_config('quiz');

    // The header.
    $visiblename = get_string('additionalquestionbehaviour', 'local_quizadditionalbehaviour');
    $mform->addElement('header', 'additionalquestionbehaviour', $visiblename);

    // Custom grading.
    $mform->addElement('selectyesno', 'customgrading', get_string('customgrading', 'local_quizadditionalbehaviour'));
    $mform->addHelpButton('customgrading', 'customgrading', 'local_quizadditionalbehaviour');
    $mform->setAdvanced('customgrading', $quizconfig->customgrading_adv);
    $mform->setDefault('customgrading', $quizconfig->customgrading);

    // Disable already correct questions.
    $visiblename = get_string('disablealreadycorrectquestions', 'local_quizadditionalbehaviour');
    $mform->addElement('selectyesno', 'disablecorrect', $visiblename);
    $mform->addHelpButton('disablecorrect', 'disablealreadycorrectquestions', 'local_quizadditionalbehaviour');
    $mform->setAdvanced('disablecorrect', $quizconfig->disablecorrect_adv);
    $mform->setDefault('disablecorrect', $quizconfig->disablecorrect);

    // Disable already correct questions and show the correct answer.
    $visiblename = get_string('disablealreadycorrectquestions_showcorrect', 'local_quizadditionalbehaviour');
    $mform->addElement('selectyesno', 'disablecorrectshowcorrect', $visiblename);
    $mform->addHelpButton('disablecorrectshowcorrect', 'disablealreadycorrectquestions_showcorrect', 'local_quizadditionalbehaviour');
    $mform->setAdvanced('disablecorrectshowcorrect', $quizconfig->disablecorrectshowcorrect_adv);
    $mform->setDefault('disablecorrectshowcorrect', $quizconfig->disablecorrectshowcorrect);

    // Disable showing the correct answer to the user.
    $visiblename = get_string('disableshowcorrectforstudent', 'local_quizadditionalbehaviour');
    $mform->addElement('selectyesno', 'disableshowcorrectforstudent', $visiblename);
    $mform->addHelpButton('disableshowcorrectforstudent', 'disableshowcorrectforstudent', 'local_quizadditionalbehaviour');
    $mform->setAdvanced('disableshowcorrectforstudent', $quizconfig->disableshowcorrectforstudent_adv);
    $mform->setDefault('disableshowcorrectforstudent', $quizconfig->disableshowcorrectforstudent);
}