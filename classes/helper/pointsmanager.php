<?php

namespace local_gamification\helper;

defined('MOODLE_INTERNAL') || die();

class pointsmanager
{
    /**
     * Updates the points for a user in a specific course.
     *
     * @param int $userid Course ID
     * @param int $courseid Course ID
     * @param string $configkey Key to fetch points from settings
     */
    public static function update_points($userid, $courseid, $configkey)
    {
        global $DB;
        $points = get_config('local_gamification', $configkey) ?: 0;

        $record = $DB->get_record('local_gamification_points', [
            'userid'   => $userid,
            'courseid' => $courseid
        ]);

        if ($record) {
            $record->points       += $points;
            $record->timemodified  = time();
            $DB->update_record('local_gamification_points', $record);
        } else {
            $DB->insert_record('local_gamification_points', [
                'userid'       => $userid,
                'courseid'     => $courseid,
                'points'       => $points,
                'timemodified' => time(),
            ]);
        }
    }
}
