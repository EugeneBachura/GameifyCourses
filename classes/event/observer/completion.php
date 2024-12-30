<?php

namespace local_gamification\event\observer;

defined('MOODLE_INTERNAL') || die();

use core\event\course_module_completion_updated;
use local_gamification\helper\pointsmanager;

class completion
{

    /**
     * Handles the event \core\event\course_module_completion_updated
     *
     * @param course_module_completion_updated $event
     */
    public static function handle(course_module_completion_updated $event)
    {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        pointsmanager::update_points($userid, $courseid, 'points_task');
    }
}
