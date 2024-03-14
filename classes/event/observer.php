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
 * Event observer for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizadditionalbehaviour\event;

// No direct access.
defined('MOODLE_INTERNAL') || die();

use mod_quiz\event\attempt_submitted;
use local_quizadditionalbehaviour\quiz_attempt;
use mod_quiz\event\user_override_created;
use mod_quiz\event\user_override_updated;
use mod_quiz\event\group_override_created;
use mod_quiz\event\group_override_updated;

class observer {
    public static function quiz_attempt_submitted(attempt_submitted $event) : void {
        global $DB;

        // Pull all the things out of the event data.
        $quizattempttable = $event->objecttable;
        $quizattemptid = $event->objectid;
        $userquizattempt = $event->get_record_snapshot($quizattempttable, $quizattemptid);
        $quizcmid = $event->contextinstanceid;

        $coursemodule = get_coursemodule_from_id('quiz', $quizcmid);
        $quiz = $DB->get_record('quiz', ['id' => $coursemodule->instance]);
        $course = $DB->get_record('course', ['id' => $event->courseid]);

        $quizattempt = new quiz_attempt($userquizattempt, $quiz, $coursemodule, $course, false);

        if (!$quizattempt->disablecorrect()) {
            return;
        }

        // Make sure that any questions that have been answered correctly by the learner are filled in.
        $thelastquizattempt = $quizattempt->get_last_complete_attempt();
        $commentstring = get_string('manualgradecomment', 'local_quizadditionalbehaviour');
        foreach ($quizattempt->get_slots('all') as $slot) {
            if ($thelastquizattempt[$slot]->correct) {
                $quizattempt->manual_grade_question($slot, $commentstring, $thelastquizattempt[$slot]->maxMark, 1);
            }
        }
    }

    public static function quiz_override_created(user_override_created $event) : void {
        global $DB;

        $override = array(
            'id' => $event->objectid,
            'granterid' => $event->userid
        );

        $validation = array(
            'id' => $event->objectid,
            'quiz' => $event->contextinstanceid,
            'userid' => empty($event->relateduserid)? null : $event->relateduserid/* ,
            'groupid' => empty($event->other['groupid'])? null : $event->other['groupid'] */
        );

        if ($DB->record_exists('quiz_overrides', $validation)) {
            $DB->update_record('quiz_overrides', $override);
        }
    }
}