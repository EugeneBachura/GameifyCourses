<?php

namespace local_gamification\event\observer;

defined('MOODLE_INTERNAL') || die();

use mod_forum\event\post_created;
use local_gamification\helper\pointsmanager;

class forum
{

    /**
     * Handles the event when a forum post is created
     *
     * @param post_created $event
     */
    public static function handle(post_created $event)
    {
        $userid = $event->userid;
        $courseid = $event->courseid;

        pointsmanager::update_points($userid, $courseid, 'points_forum');
    }
}
