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
 * Quiz things for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Andrew Chandler <andrew.chandler@skills-group.org>
 * @copyright   2024 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizadditionalbehaviour;

use quiz_lib as core_quiz_lib;
use settings_navigation;
use navigation_node;
use moodle_url;
use pix_icon;

// No direct access.
defined('MOODLE_INTERNAL') || die();

class quiz_lib extends core_quiz_lib {
    function quiz_extend_settings_navigation(settings_navigation $settings, navigation_node $quiznode) {
        global $CFG;
    
        // Require {@link questionlib.php}
        // Included here as we only ever want to include this file if we really need to.
        require_once($CFG->libdir . '/questionlib.php');
    
        // We want to add these new nodes after the Edit settings node, and before the
        // Locally assigned roles node. Of course, both of those are controlled by capabilities.
        debugging("quiz_lib");
        $keys = $quiznode->get_children_key_list();
        $beforekey = null;
        $i = array_search('modedit', $keys);
        if ($i === false and array_key_exists(0, $keys)) {
            $beforekey = $keys[0];
        } else if (array_key_exists($i + 1, $keys)) {
            $beforekey = $keys[$i + 1];
        }
    
        if (has_any_capability(['mod/quiz:manageoverrides', 'mod/quiz:viewoverrides'], $settings->get_page()->cm->context)) {
            $url = new moodle_url('/mod/quiz/overrides.php', ['cmid' => $settings->get_page()->cm->id, 'mode' => 'user']);
            $node = navigation_node::create(get_string('overrides', 'quiz'),
                        $url, navigation_node::TYPE_SETTING, null, 'mod_quiz_useroverrides');
            $settingsoverride = $quiznode->add_node($node, $beforekey);
        }
    
        if (has_capability('mod/quiz:manage', $settings->get_page()->cm->context)) {
            $node = navigation_node::create(get_string('questions', 'quiz'),
                new moodle_url('/mod/quiz/edit.php', array('cmid' => $settings->get_page()->cm->id)),
                navigation_node::TYPE_SETTING, null, 'mod_quiz_edit', new pix_icon('t/edit', ''));
            $quiznode->add_node($node, $beforekey);
        }
    
        if (has_capability('mod/quiz:preview', $settings->get_page()->cm->context)) {
            $url = new moodle_url('/mod/quiz/startattempt.php',
                    array('cmid' => $settings->get_page()->cm->id, 'sesskey' => sesskey()));
            $node = navigation_node::create(get_string('preview', 'quiz'), $url,
                    navigation_node::TYPE_SETTING, null, 'mod_quiz_preview',
                    new pix_icon('i/preview', ''));
            $previewnode = $quiznode->add_node($node, $beforekey);
            $previewnode->set_show_in_secondary_navigation(false);
        }
    }
}