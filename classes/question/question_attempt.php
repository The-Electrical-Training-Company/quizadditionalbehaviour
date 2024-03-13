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
 * Question attempt override for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizadditionalbehaviour\question;

// No direct access.
defined('MOODLE_INTERNAL') || die();

use question_attempt as core_question_attempt;
use local_quizadditionalbehaviour\question\behaviour\deferredfeedback\qbehaviour_deferredfeedback as local_qbehaviour_deferredfeedback;
use question_usage_observer;
use question_bank;
use question_engine;
use question_attempt_step;
use Iterator;
use coding_exception;
use Exception;

class question_attempt extends core_question_attempt {
    public function render($options, $number, $page = null) {
        $this->ensure_question_initialised();
        if (is_null($page)) {
            global $PAGE;
            $page = $PAGE;
        }
        $qoutput = $page->get_renderer('core', 'question');
        $qtoutput = $this->question->get_renderer($page);
        return $this->behaviour->render($options, $number, $qoutput, $qtoutput);
    }

    /**
     * Create a question_attempt_step from records loaded from the database.
     *
     * For internal use only.
     *
     * @param Iterator $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @param question_usage_observer $observer the observer that will be monitoring changes in us.
     * @param string $preferredbehaviour the preferred behaviour under which we are operating.
     * @return question_attempt The newly constructed question_attempt.
     */
    public static function load_from_records($records, $questionattemptid,
        question_usage_observer $observer, $preferredbehaviour) {
        $record = $records->current();
        while ($record->questionattemptid != $questionattemptid) {
            $records->next();
            if (!$records->valid()) {
                throw new coding_exception("Question attempt {$questionattemptid} not found in the database.");
            }
            $record = $records->current();
        }

        try {
            $question = question_bank::load_question($record->questionid);
        } catch (Exception $e) {
            // The question must have been deleted somehow. Create a missing
            // question to use in its place.
            $question = question_bank::get_qtype('missingtype')->make_deleted_instance(
                $record->questionid, $record->maxmark + 0);
        }

        $qa = new question_attempt($question, $record->questionusageid,
            null, $record->maxmark + 0);
        $qa->set_database_id($record->questionattemptid);
        $qa->set_slot($record->slot);
        $qa->variant = $record->variant + 0;
        $qa->minfraction = $record->minfraction + 0;
        $qa->maxfraction = $record->maxfraction + 0;
        $qa->set_flagged($record->flagged);
        $qa->questionsummary = $record->questionsummary;
        $qa->rightanswer = $record->rightanswer;
        $qa->responsesummary = $record->responsesummary;
        $qa->timemodified = $record->timemodified;

        $qa->behaviour = question_engine::make_behaviour(
            $record->behaviour, $qa, $preferredbehaviour);
        $qa->observer = $observer;

        // If attemptstepid is null (which should not happen, but has happened
        // due to corrupt data, see MDL-34251) then the current pointer in $records
        // will not be advanced in the while loop below, and we get stuck in an
        // infinite loop, since this method is supposed to always consume at
        // least one record. Therefore, in this case, advance the record here.
        if (is_null($record->attemptstepid)) {
            $records->next();
        }

        $i = 0;
        $autosavedstep = null;
        $autosavedsequencenumber = null;
        while ($record && $record->questionattemptid == $questionattemptid && !is_null($record->attemptstepid)) {
            $sequencenumber = $record->sequencenumber;
            $nextstep = question_attempt_step::load_from_records($records, $record->attemptstepid,
                $qa->get_question(false)->get_type_name());

            if ($sequencenumber < 0) {
                if (!$autosavedstep) {
                    $autosavedstep = $nextstep;
                    $autosavedsequencenumber = -$sequencenumber;
                } else {
                    // Old redundant data. Mark it for deletion.
                    $qa->observer->notify_step_deleted($nextstep, $qa);
                }
            } else {
                $qa->steps[$i] = $nextstep;
                $i++;
            }

            if ($records->valid()) {
                $record = $records->current();
            } else {
                $record = false;
            }
        }

        if ($autosavedstep) {
            if ($autosavedsequencenumber >= $i) {
                $qa->autosavedstep = $autosavedstep;
                $qa->steps[$i] = $qa->autosavedstep;
            } else {
                $qa->observer->notify_step_deleted($autosavedstep, $qa);
            }
        }

        return $qa;
    }
}