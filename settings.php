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
 * Settings for local_quizadditionalbehaviour.
 *
 * @package     local_quizadditionalbehaviour
 * @author      Donald Barrett <donaldb@skills.org.nz>
 * @copyright   2022 onwards, Skills Consulting Group Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No direct access.
defined('MOODLE_INTERNAL') || die();

// Only render for users that have the site config capability.
if ($hassiteconfig) {
    // Used for the component name in the get_string function.
    $componentname = 'local_quizadditionalbehaviour';

    // Container for the settings.
    $settings = [];

    $settings[] = new admin_setting_configcheckbox_with_advanced(
        'quiz/customgrading',
        get_string('customgrading', $componentname),
        get_string('customgrading_help', $componentname),
        ['value' => 0, 'adv' => false]
    );

    $settings[] = new admin_setting_configcheckbox_with_advanced(
        'quiz/disablecorrect',
        get_string('disablealreadycorrectquestions', $componentname),
        get_string('disablealreadycorrectquestions_help', $componentname),
        ['value' => 0, 'adv' => true]
    );

    $settings[] = new admin_setting_configcheckbox_with_advanced(
        'quiz/disablecorrectshowcorrect',
        get_string('disablealreadycorrectquestions_showcorrect', $componentname),
        get_string('disablealreadycorrectquestions_showcorrect_help', $componentname),
        ['value' => 0, 'adv' => true]
    );

    $settings[] = new admin_setting_configcheckbox_with_advanced(
        'quiz/disableshowcorrectforstudent',
        get_string('disableshowcorrectforstudent', $componentname),
        get_string('disableshowcorrectforstudent_help', $componentname),
        ['value' => 0, 'adv' => true]
    );

    // Make the settings page and add all the settings.
    $settingspage = new admin_settingpage($componentname, get_string('pluginname', $componentname));
    foreach ($settings as $setting) {
        $settingspage->add($setting);
    }

    // Add the settings page to the admin tree.
    $ADMIN->add('localplugins', $settingspage);
}
