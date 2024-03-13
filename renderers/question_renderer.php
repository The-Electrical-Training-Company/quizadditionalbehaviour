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
 * Render overrides for local_quizadditionalbehaviour.
 *
 * Todo: This file must be copied into theme/renderers/question_renderer. See README.md.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/engine/renderer.php');

// Todo: Rename local_quizadditionalbehaviour to theme_themename after it has been moved. See README.md.
class local_quizadditionalbehaviour_core_question_renderer extends \core_question_renderer {
    public function question(
            question_attempt $qa,
            qbehaviour_renderer $behaviouroutput,
            qtype_renderer $qtoutput,
            question_display_options $options,
            $number
    ) {
        // Custom question rendering.
        if (isset($options->displayAnswerOnly) && $options->displayAnswerOnly) {
            return $this->formulation($qa, $behaviouroutput, $qtoutput, $options);
        }

        $stateclass = $qa->get_state_class($options->correctness && $qa->has_marks());
        if (isset($options->passed)) {
            $stateclass = 'corecode correctness correct';
        }

        $output = '';
        if (isset($options->passed) && isset($options->passed_question) && $options->passed_question) {
            $output .= html_writer::start_tag('div', [
                'id' => $options->passed_question->get_outer_question_div_unique_id(),
                'class' => implode(' ', [
                    'que',
                    $options->passed_question->get_question(false)->get_type_name(),
                    $options->passed_question->get_behaviour_name(),
                    $stateclass,
                ]),
            ]);
        } else {
            // This is part of the original working.
            $output .= html_writer::start_tag('div', [
                'id' => $qa->get_outer_question_div_unique_id(),
                'class' => implode(' ', [
                    'que',
                    $qa->get_question(false)->get_type_name(),
                    $qa->get_behaviour_name(),
                    $stateclass,
                ]),
            ]);
        }

        if (isset($options->passed)) {
            $output .= html_writer::start_tag('div', ['class' => 'info']);
            $output .= '';
            $output .= $this->number($number);
            // Do not rename the language string component here.
            $output .= html_writer::tag('div', get_string('previouslycompleted', 'local_quizadditionalbehaviour'),
                ['class' => 'state']);
            $output .= $this->edit_question_link($qa, $options);
            $output .= html_writer::end_tag('div');
        } else {
            $output .= html_writer::tag('div',
                $this->info($qa, $behaviouroutput, $qtoutput, $options, $number),
                ['class' => 'info']);
        }

        $output .= html_writer::start_tag('div', ['class' => 'content']);

        $question = $qa->get_question();
        if (isset($options->passed)) {
            $output .= html_writer::start_tag('div', ['class' => 'formulation']);
            // Do not rename the language string component here.
            $output .= html_writer::tag('div', $this->add_part_heading($qtoutput->formulation_heading(), get_string('alreadyansweredcorrectly', 'local_quizadditionalbehaviour')), ['class' => '']);

            if (isset($options->passed_question) && $options->passed_question) {
                $output .= html_writer::start_tag('div', ['class' => 'hidden']);
                $output .= $this->formulation($options->passed_question, $behaviouroutput, $qtoutput, $options);
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('div');
                $passed_question_options = $options;
                $passed_question_options->readonly = true;
                $output .= html_writer::tag('div', $this->formulation($options->passed_question, $behaviouroutput, $qtoutput, $passed_question_options), ['class' => 'formulation clearfix']);
            } else {
                $output .= html_writer::start_tag('div', ['class' => 'hidden']);
                $output .= $this->formulation($qa, $behaviouroutput, $qtoutput, $options);
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('div');
            }
        } else {
            $output .= html_writer::tag('div',
                $this->add_part_heading($qtoutput->formulation_heading(),
                    $this->formulation($qa, $behaviouroutput, $qtoutput, $options)),
                ['class' => 'formulation clearfix']);
            if (isset($options->viewprevanswers) && $options->viewprevanswers === 1 && $number !== 'i') {
                $output .= html_writer::start_tag('div', ['class' => 'clearfix formulation']);
                $output .= '<p><a class="btn btn-primary" data-toggle="collapse" href="#collapsePrevQuestions' . $number .
                    '" aria-expanded="false" aria-controls="collapsePrevQuestions' . $number . '">View Previous Attempts</a></p>';
                $output .= html_writer::start_tag('div', ['class' => 'collapse', 'id' => "collapsePrevQuestions" . $number]);
                $output .= html_writer::start_tag('div', ['class' => 'card card-body']);
                foreach ($options->prevanswers as $key => $value) {
                    $output .= '<b>Attempt ' . ($key + 1) . '</b>';
                    $output .= $value;
                }
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('div');
            }
            if (isset($options->viewcustomgrading) && $options->viewcustomgrading === 1 && $number !== 'i') {
                if ($qa->has_manual_comment() || $options->manualcommentlink) {
                    $context = [
                        'questionno' => $number,
                        'attemptid' => $options->quizattemptid,
                        'has_manual_comment' => $qa->has_manual_comment(),
                        'manual_comment' => ($qa->has_manual_comment() ? get_string('commentx', 'question', $qa->get_behaviour()->format_comment(null, null, $options->context)) : ''),
                        'slot' => $qa->get_slot(),
                        'sesskey' => sesskey(),
                        'has_grader_info' => isset($question->graderinfo),
                        'grader_info' => (isset($question->graderinfo) ?
                            $question->format_text(
                                $question->graderinfo, $question->graderinfo, $qa, get_class($question->qtype),
                                'graderinfo', $question->id) :
                            ''),
                        'manualcommentlink' => $options->manualcommentlink,
                        'manual_comment_fields' => ($options->manualcommentlink ? $behaviouroutput->manual_comment_fields($qa, $options) : ''),
                    ];
                    $output .= $this->render_from_template('core_question/grading_grade', $context);
                }
            } else {
                $output .= html_writer::nonempty_tag('div',
                    $this->add_part_heading(get_string('comments', 'question'),
                        $this->manual_comment($qa, $behaviouroutput, $qtoutput, $options)),
                    ['class' => 'comment clearfix']);
            }
            $output .= html_writer::nonempty_tag('div',
                $this->add_part_heading(get_string('feedback', 'question'),
                    $this->outcome($qa, $behaviouroutput, $qtoutput, $options)),
                ['class' => 'outcome clearfix']);
            $output .= html_writer::nonempty_tag('div',
                $this->response_history($qa, $behaviouroutput, $qtoutput, $options),
                ['class' => 'history clearfix border p-2']);
        }

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        return $output;
    }
}