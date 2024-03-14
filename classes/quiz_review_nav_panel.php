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
 * Overridden quiz_review_nav_panel for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizadditionalbehaviour;

// No direct access.
defined('MOODLE_INTERNAL') || die();

use mod_quiz_renderer;
use quiz_review_nav_panel as core_quiz_review_nav_panel;
use quiz_nav_question_button;
use quiz_nav_section_heading;

class quiz_review_nav_panel extends core_quiz_review_nav_panel {
    public function get_question_buttons() {
        if (!$this->attemptobj->disablecorrect()
         || !$this->attemptobj->disableshowcorrectforstudent()
         || !$this->attemptobj->disableshowcorrectforall()) {
            // Do the core things.
            return parent::get_question_buttons();
        }
        // Doing the non core things.
        $buttons = [];
        if ($this->attemptobj->disablecorrect()) {
            $qattempt = $this->attemptobj->get_last_complete_attempt();
        }

        foreach ($this->attemptobj->get_slots() as $slot) {
            if ($heading = $this->attemptobj->get_heading_before_slot($slot)) {
                $buttons[] = new quiz_nav_section_heading(format_string($heading));
            }

            $qa = $this->attemptobj->get_question_attempt($slot);

            // We actually want the nav to to show correctness
            // So we preserve the old value here
            if ($this->attemptobj->disableshowcorrectforstudent() || $this->attemptobj->disableshowcorrectforall()) {
                $showcorrectness = ($this->options->correctness || $this->options->truecorrectness) && $qa->has_marks();
            } else {
                $showcorrectness = $this->options->correctness && $qa->has_marks();
            }

            $button = new quiz_nav_question_button();
            $button->id          = 'quiznavbutton' . $slot;
            $button->number      = $this->attemptobj->get_question_number($slot);
            $button->stateclass  = $qa->get_state_class($showcorrectness);
            $button->navmethod   = $this->attemptobj->get_navigation_method();
            if (!$showcorrectness && $button->stateclass == 'notanswered') {
                $button->stateclass = 'complete';
            }
            $button->statestring = $this->get_state_string($qa, $showcorrectness);
            $button->page        = $this->attemptobj->get_question_page($slot);
            $button->currentpage = $this->showall || $button->page == $this->page;
            $button->flagged     = $qa->is_flagged();
            $button->url         = $this->get_question_url($slot);
            if ($this->attemptobj->is_blocked_by_previous_question($slot)) {
                $button->url = null;
                $button->stateclass = 'blocked';
                $button->statestring = get_string('questiondependsonprevious', 'quiz');
            }

            if ($this->attemptobj->disablecorrect()) {
                if ($qattempt[$slot]->correct) {
                    $button->stateclass = 'correct';
                }
            }

            $buttons[] = $button;
        }

        return $buttons;
    }
}