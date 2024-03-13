Local quiz additional behaviour
===============================

A local plugin that adds additional behaviour to the quiz activity module.

The additional behaviours are:

1. Hide a users quiz answer that was answered correctly in the immediate previous attempt and shift their previous correct answer to the new attempt.
2. Hide or show the correctly answered question from the immediate previous attempt.
3. Custom grading functionality.

These customisations were pulled from a bunch of core files that were modified in order to achieve this behaviour.

The local plugin is used to override the core quiz and question behaviour classes and renderers so that custom functionality can happen.

The local plugin does not work on its own, it requires a custom theme with a bunch of render overrides.

How to make the things work
===========================

For the 'additional quiz behaviour' to work, the question_renderer and the quiz_renderer needs to be overridden. 

If you are using the core Moodle boost theme, use the patch file in the directory ./patches/additional_theme_renderers.patch.
See [https://docs.moodle.org/dev/How_to_apply_a_patch](https://docs.moodle.org/dev/How_to_apply_a_patch)

If you have a custom theme, add the additional things to your custom theme as below.

To override the question_engine renderer:

1. Copy the file 'renderers/question_renderer.php' to the theme directory in a subdirectory named 'renderers'.
2. Rename the class in 'renderers/question_renderer.php' from 'local_quizadditionalbehaviour_core_question_renderer' to 'theme_themename_core_question_renderer'
where 'themename' is replaced with the name of the theme without the 'theme_' prefix.
3. Create a file named 'renderers.php' in the theme root directory. If the file already exists, skip this step.
4. Include the 'question_renderer.php' file in the 'renderers.php' file using `require_once('renderers/question_renderer.php');`

To override the other quiz and question components:

1. Copy the file 'classes/output/mod_quiz_renderer.php' to the theme directory in a subdirectory named 'classes/output'.
2. Rename the 'namespace' from `namespace local_quizadditionalbehaviour\output;` to `namespace theme_themename\output;` where the 'themename' is replaced with
the name of the theme without the 'theme_' prefix.
3. If you would like to be pedantic about it, you may update all the references to local_quizadditionalbehaviour to theme_themename in the comments and other places.

Note: The language strings don't need to be updated to theme_themename because all the language strings are defined in this local plugin.

Finally, purge the caches and it "should" work :)

License
=======

2022 Skills Consulting Group. 

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation,
either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see https://www.gnu.org/licenses/.