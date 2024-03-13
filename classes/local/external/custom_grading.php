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
 * External webservice functions for custom grading for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizadditionalbehaviour\local\external;

// No direct access.
defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use local_quizadditionalbehaviour\quiz_attempt;
use stdClass;
use question_display_options;
use html_writer;
use context_module;
use context_user;
use Throwable;

class custom_grading extends external_api {
    public static function get_parameters() : external_function_parameters {
        return new external_function_parameters([
            'attemptid' => new external_value(PARAM_INT, 'Attempt ID'),
            'slot' => new external_value(PARAM_INT, 'Question slot in attempt'),
        ]);
    }

    public static function get_returns() : external_single_structure {
        return new external_single_structure([
            'comment' => new external_value(PARAM_RAW, 'Returned comment'),
            'grade' => new external_value(PARAM_RAW, 'Returned grade'),
            'statestring' => new external_value(PARAM_RAW, 'Returned state'),
            'stateclass' => new external_value(PARAM_RAW, 'Returned class for nav buttons'),
            'summarymark' => new external_value(PARAM_RAW, 'Returned summarymark'),
            'summarygrade' => new external_value(PARAM_RAW, 'Returned summarygrade'),
            'error' => new external_value(PARAM_RAW, 'Returned error, blank for no error'),
        ]);
    }

    public static function get(int $attemptid, int $slot) : array {
        global $CFG, $PAGE;
        require_once($CFG->dirroot.'/mod/quiz/locallib.php');

        // Parameter validation.
        $params = self::validate_parameters(
            self::get_parameters(), ['attemptid' => $attemptid, 'slot' => $slot,]
        );
        $attemptid = $params['attemptid'];
        $slot = $params['slot'];

        // Handle the errors through the core things.
        $attemptobj = quiz_create_attempt_handling_errors($attemptid);

        // Now all the core things have been done, use our overridden quiz_attempt object.
        unset($attemptobj);
        $attemptobj = quiz_attempt::create($attemptid);

        // Can only grade finished attempts.
        if (!$attemptobj->is_finished()) {
            print_error('attemptclosed', 'quiz');
        }

        // Check login and permissions.
        require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
        $attemptobj->require_capability('mod/quiz:grade');

        // Get everything we need to get everything we need to re-render
        $qa = $attemptobj->get_question_attempt($slot);
        $attempt = $attemptobj->get_attempt();
        $quiz = $attemptobj->get_quiz();
        $behaviour = $qa->get_behaviour();

        $options = $attemptobj->get_display_options(true);
        $showcorrectness = $options->correctness && $qa->has_marks();

        $qoutput = $PAGE->get_renderer('core', 'question');

        // Get grade (mark summary).
        if (!$options->marks) {
            $marksummary = '';
        } else if ($qa->get_max_mark() == 0) {
            $marksummary = get_string('notgraded', 'question');
        } else if ($options->marks == question_display_options::MAX_ONLY || is_null($qa->get_fraction())) {
            $marksummary = $qoutput->standard_marked_out_of_max($qa, $options);
        } else {
            $marksummary = $qoutput->standard_mark_out_of_max($qa, $options);
        }

        // Generate state class and state string. Not entirely sure that the state string is used.
        $stateclass  = $qa->get_state_class($showcorrectness);
        $statestring = $qa->get_state_string($showcorrectness);

        if (!$showcorrectness && $stateclass == 'notanswered') {
            $stateclass = 'complete';
        }
        if ($attemptobj->is_blocked_by_previous_question($slot)) {
            $stateclass = 'blocked';
            $statestring = get_string('questiondependsonprevious', 'quiz');
        }

        $classes = ['qnbutton', $stateclass, $attemptobj->get_navigation_method(), 'btn', 'btn-secondary', 'thispage'];
        if ($qa->is_flagged()) {
            $classes[] = 'flagged';
        }

        // Build new summary info.
        $summarymark = '';
        if ($quiz->grade != $quiz->sumgrades) {
            $a = new stdClass();
            $a->grade = quiz_format_grade($quiz, $attempt->sumgrades);
            $a->maxgrade = quiz_format_grade($quiz, $quiz->sumgrades);
            $summarymark = get_string('outofshort', 'quiz', $a);
        }

        // Now the scaled grade (for the summary).
        $a = new stdClass();
        $grade = quiz_rescale_grade($attempt->sumgrades, $quiz, false);
        if (is_null($grade)) {
            $summarygrade = quiz_format_grade($quiz, $grade);
        } else {
            $a->grade = html_writer::tag('b', quiz_format_grade($quiz, $grade));
            $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
            if ($quiz->grade != 100) {
                $a->percent = html_writer::tag('b', format_float($attempt->sumgrades * 100 / $quiz->sumgrades, 0));
                $summarygrade = get_string('outofpercent', 'quiz', $a);
            } else {
                $summarygrade = get_string('outof', 'quiz', $a);
            }
        }

        $context = context_module::instance($attemptobj->get_cm()->id);

        return [
            'comment' => $behaviour->format_comment(null, null, $context),
            'grade' => $marksummary,
            'statestring' => $qa->get_state_string($showcorrectness),
            'stateclass' => implode(' ', $classes),
            'summarymark' => $summarymark,
            'summarygrade' => $summarygrade,
            'error' => '',
        ];
    }

    public static function set_parameters() : external_function_parameters {
        return new external_function_parameters([
            'attemptid' => new external_value(PARAM_INT, 'Attempt ID'),
            'slot' => new external_value(PARAM_INT, 'Question slot in attempt'),
            'comment' => new external_value(PARAM_RAW, 'Comment to save'),
            'commentformat' => new external_value(PARAM_INT, 'Comment format'),
            'grade' => new external_value(PARAM_RAW, 'Grade to save'),
        ]);
    }

    public static function set_returns() : external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_RAW, 'Returned result'),
        ]);
    }

    public static function set(int $attemptid, int $slot, $comment, int $commentformat, $grade) : array {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot.'/mod/quiz/locallib.php');
        require_once($CFG->dirroot.'/lib/filelib.php');

        // Parameter validation.
        $params = self::validate_parameters(
            self::set_parameters(), [
                'attemptid' => $attemptid,
                'slot' => $slot,
                'comment' => $comment,
                'commentformat' => $commentformat,
                'grade' => $grade,
            ]
        );
        $attemptid = $params['attemptid'];
        $slot = $params['slot'];
        $comment = $params['comment'];
        $commentformat = $params['commentformat'];
        $grade = $params['grade'];
        try {
            // Create attemptobj with all the errors, then our version.
            $attemptobj = quiz_create_attempt_handling_errors($attemptid);
            unset($attemptobj);
            $attemptobj = quiz_attempt::create($attemptid);
            // Can only grade finished attempts.
            if (!$attemptobj->is_finished()) {
                print_error('attemptclosed', 'quiz');
            }
            // Check login and permissions.
            require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
            $attemptobj->require_capability('mod/quiz:grade');
            $coursemodule = $attemptobj->get_cm();
            $usercontext = context_user::instance($USER->id);
            $modulecontext = context_module::instance($coursemodule->id);
            $expression = '/\/draftfile.php\/'.$usercontext->id.'\/user\/draft\/(\d+)\//';
            $isuserfileareapresent = preg_match($expression, $comment, $matches);
            if ($isuserfileareapresent && isset($matches[1])) {
                $commentlinked = file_rewrite_urls_to_pluginfile($comment, $matches[1]);
            } else {
                $commentlinked = $comment;
            }
            $attemptobj->manual_grade_question($slot, $commentlinked, $grade, $commentformat);
            if ($isuserfileareapresent) {
                $qa = $attemptobj->get_question_attempt($slot);
                $laststep = $DB->get_records('question_attempt_steps', ['questionattemptid' => $qa->get_database_id()]);
                $laststep = end($laststep);

                // Params for saving the draft area files.
                $draftitemid = intval($matches[1]);
                $component = 'question';
                $filearea = 'response_bf_comment';
                $itemid = $laststep->id;
                file_save_draft_area_files($draftitemid, $modulecontext->id, $component, $filearea, $itemid, null, $comment);
            }
            return ['result' => 'done'];
        } catch (Throwable $error) {
            return ['result' => 'error'];
        }
    }
}