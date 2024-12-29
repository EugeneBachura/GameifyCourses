<?php

namespace local_gamification\event\observer;

defined('MOODLE_INTERNAL') || die();

use mod_quiz\event\attempt_submitted;
use local_gamification\helper\pointsmanager;

class quiz
{

    /**
     * Handles the event \mod_quiz\event\attempt_submitted
     *
     * @param attempt_submitted $event
     */
    public static function handle(attempt_submitted $event)
    {
        $userid = $event->userid;
        $courseid = $event->courseid;

        pointsmanager::update_points($userid, $courseid, 'points_quiz');
    }
}
