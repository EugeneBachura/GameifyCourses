<?php

/**
 * Event definitions for the Gamification plugin.
 *
 * @package   local_gamification
 * @copyright 2025 Eugene Bachura
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_module_completion_updated',
        'callback'  => '\local_gamification\event\observer\completion::handle',
    ],
    [
        'eventname' => '\mod_forum\event\post_created',
        'callback'  => '\local_gamification\event\observer\forum::handle',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback'  => '\local_gamification\event\observer\quiz::handle',
    ],
];
