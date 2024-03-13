# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project tries to adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

There is nothing that is unreleased

## [Version 2022032405, release v3.9.5]
### Added
- Missing js stuff for custom grading
- Missing template for custom grading
- Lang string for custom grading error

### Updated
- Patch file for renderer to use template from local_quizadditionalbehaviour

## [Version 2022032404, release v3.9.4]
### Added
- Patch file for adding the other theme

## [Version 2022032403, release v3.9.3]
### Fixed
- Well, tidied up some code formatting because for renderers

## [Version 2022032402, release v3.9.2] - 2022-05-22
### Added
- Instructions in the README file to make the additional behaviour work in the theme
- The renderers that need to be moved into the theme. Instructions in the README

### Fixed
- Incorrect dates in previous change log entries

## [Version 2022032401, release v3.9.1] - 2022-04-22
### Fixed
- Fixed a whole bunch of missed typed things

## [Version 2022032400, release v3.9.0] - 2022-04-22
### Added
- Additional fields to the core quiz table
- Overridden question and quiz classes for theme renderer overrides to use
- Settings page to add additional things to the quiz settings
- A bunch of language strings for the additional behaviour
- Webservice functions for custom grading
- Event observer for when a quiz attempt is submitted to manually grade a question if it was already answered correctly in the previous attempt
- A bunch of classes for the nav panels for the theme render overrides to use
- Overridden quiz attempt and quiz classes
- Question behaviour, question attempt, question engine and quba overrides
- The CHANGELOG
- The README