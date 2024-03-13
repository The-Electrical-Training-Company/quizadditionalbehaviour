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
 * Quiz renderer override for local_quizadditionalbehaviour.
 *
 * Todo: This file must be
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Todo: Update namespace to theme_themename\output.
namespace local_quizadditionalbehaviour\output;

// No direct access.
use local_quizadditionalbehaviour\quiz;
use local_quizadditionalbehaviour\quiz_attempt;
use local_quizadditionalbehaviour\quiz_attempt_nav_panel;
use local_quizadditionalbehaviour\quiz_review_nav_panel;
use mod_quiz_renderer as core_mod_quiz_renderer;
use mod_quiz_display_options as core_mod_quiz_display_options;
use quiz_nav_panel_base as core_quiz_nav_panel_base;
use quiz_attempt_nav_panel as core_quiz_attempt_nav_panel;
use quiz_review_nav_panel as core_quiz_review_nav_panel;
use quiz_attempt as core_quiz_attempt;
use html_table;
use html_table_cell;
use html_writer;
use question_display_options;
use coding_exception;

defined('MOODLE_INTERNAL') || die();

class mod_quiz_renderer extends core_mod_quiz_renderer {
    public function summary_page($attemptobj, $displayoptions) {
        $attemptobj = quiz_attempt::create($attemptobj->get_attemptid());
        return parent::summary_page($attemptobj, $displayoptions);
    }

    public function summary_table($attemptobj, $displayoptions) {
        $attemptobj = quiz_attempt::create($attemptobj->get_attemptid());
        $displayoptions = $attemptobj->get_display_options(false);
        $lastcompleteattempt = $attemptobj->get_last_complete_attempt();
        if (empty($lastcompleteattempt)) {
            // Do the core things.
            return parent::summary_table($attemptobj, $displayoptions);
        } else {
            // Prepare the summary table header.
            $table = new html_table();
            $table->attributes['class'] = 'generaltable quizsummaryofattempt boxaligncenter';
            $table->head = [get_string('question', 'quiz'), get_string('status', 'quiz')];
            $table->align = ['left', 'left'];
            $table->size = ['', ''];
            $markscolumn = $displayoptions->marks >= question_display_options::MARK_AND_MAX;
            if ($markscolumn) {
                $table->head[] = get_string('marks', 'quiz');
                $table->align[] = 'left';
                $table->size[] = '';
            }
            $tablewidth = count($table->align);
            $table->data = [];

            // Get the summary info for each question.
            $slots = $attemptobj->get_slots();

            foreach ($slots as $slot) {
                // Add a section headings if we need one here.
                $heading = $attemptobj->get_heading_before_slot($slot);
                if ($heading) {
                    $cell = new html_table_cell(format_string($heading));
                    $cell->header = true;
                    $cell->colspan = $tablewidth;
                    $table->data[] = [$cell];
                    $table->rowclasses[] = 'quizsummaryheading';
                }

                // Don't display information items.
                if (!$attemptobj->is_real_question($slot)) {
                    continue;
                }

                // Real question, show it.
                $flag = '';
                if ($attemptobj->is_question_flagged($slot)) {
                    // Quiz has custom JS manipulating these image tags - so we can't use the pix_icon method here.
                    $flag = html_writer::empty_tag('img', [
                        'src' => $this->image_url('i/flagged'),
                        'alt' => get_string('flagged', 'question'),
                        'class' => 'questionflag icon-post',
                    ]);
                }

                // Change the display of things if this question has been answered correctly.
                if (!empty($lastcompleteattempt) && isset($lastcompleteattempt[$slot])) {
                    $questionstatus = get_string('previouslycompleted', 'local_quizadditionalbehaviour');
                } else {
                    $questionstatus = $attemptobj->get_question_status($slot, $displayoptions->correctness);
                }
                $attemptquestionnumber = $attemptobj->get_question_number($slot) . $flag;
                $attemptquestionstatus = $attemptobj->get_question_status($slot, $questionstatus);
                $row = [];
                if ($attemptobj->can_navigate_to($slot)) {
                    $row[] = html_writer::link($attemptobj->attempt_url($slot), $attemptquestionnumber);
                } else {
                    $row[] = $attemptquestionnumber;
                }
                $row[] = $attemptquestionstatus;

                // Continue with the core things.
                if ($markscolumn) {
                    $row[] = $attemptobj->get_question_mark($slot);
                }
                $table->data[] = $row;
                $questionstateclass = $attemptobj->get_question_state_class($slot, $displayoptions->correctness);
                $table->rowclasses[] = 'quizsummary' . $slot . ' ' . $questionstateclass;
            }

            // Print the summary table.
            $output = html_writer::table($table);

            return $output;
        }
        // Never reached.
    }

    public function attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id, $nextpage) {
        $attemptobj = quiz_attempt::create($attemptobj->get_attemptid());
        return parent::attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id, $nextpage);
    }

    public function attempt_form($attemptobj, $page, $slots, $id, $nextpage) {
        $attemptobj = quiz_attempt::create($attemptobj->get_attemptid());
        return parent::attempt_form($attemptobj, $page, $slots, $id, $nextpage);
    }

    public function review_page(
            core_quiz_attempt $attemptobj,
            $slots,
            $page,
            $showall,
            $lastpage,
            core_mod_quiz_display_options $displayoptions,
            $summarydata
    ) {
        $attemptobj = quiz_attempt::create($attemptobj->get_attemptid());
        $displayoptions = $attemptobj->get_display_options(true);
        return parent::review_page($attemptobj, $slots, $page, $showall, $lastpage, $displayoptions, $summarydata);
    }

    public function navigation_panel(core_quiz_nav_panel_base $panel) {
        $attempt = required_param('attempt', PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $quizattempt = quiz_attempt::create($attempt);
        $quizdisplayoptions = $quizattempt->get_display_options(true);
        if ($panel instanceof core_quiz_attempt_nav_panel) {
            $panel = new quiz_attempt_nav_panel($quizattempt, $quizdisplayoptions, $page, false);
        } else if ($panel instanceof core_quiz_review_nav_panel) {
            $panel = new quiz_review_nav_panel($quizattempt, $quizdisplayoptions, $page, false);
        } else {
            throw new coding_exception('invalid quiz_attempt_navpanel');
        }

        return parent::navigation_panel($panel);
    }
}