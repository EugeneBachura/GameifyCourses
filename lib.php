<?php

/**
 * Library for the Gamification plugin.
 *
 * Contains utility functions and logic for gamification features.
 *
 * @package   local_gamification
 * @copyright 2025 Eugene Bachura <eugene.bachura@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for gamification plugin.
 */
class local_gamification_observer
{

    /**
     * Award points when a user completes an activity.
     *
     * @param \core\event\course_module_completion_updated $event
     */
    public static function award_points($event)
    {
        self::update_points($event->relateduserid, $event->courseid, 'points_task');
    }

    /**
     * Award points for forum post creation.
     *
     * @param \mod_forum\event\post_created $event
     */
    public static function award_points_for_forum($event)
    {
        self::update_points($event->userid, $event->courseid, 'points_forum');
    }

    /**
     * Award points for quiz submission.
     *
     * @param \mod_quiz\event\attempt_submitted $event
     */
    public static function award_points_for_quiz($event)
    {
        self::update_points($event->userid, $event->courseid, 'points_quiz');
    }

    /**
     * Update points for a specific user and course.
     *
     * @param int $userid
     * @param int $courseid
     * @param string $configkey
     */
    private static function update_points($userid, $courseid, $configkey)
    {
        global $DB;

        $points = get_config('local_gamification', $configkey);

        $record = $DB->get_record('local_gamification_points', [
            'userid' => $userid,
            'courseid' => $courseid,
        ]);

        if ($record) {
            $record->points += $points;
            $record->timemodified = time();
            $DB->update_record('local_gamification_points', $record);
        } else {
            $DB->insert_record('local_gamification_points', [
                'userid' => $userid,
                'courseid' => $courseid,
                'points' => $points,
                'timemodified' => time(),
            ]);
        }
    }
}


/**
 * Extends the course settings navigation with the Gamification link.
 *
 * @param settings_navigation $settingsnav
 * @param context $context
 */
function local_gamification_extend_settings_navigation(settings_navigation $settingsnav, context $context)
{
    if ($context->contextlevel !== CONTEXT_COURSE) {
        return;
    }

    $courseid = $context->instanceid;

    $courseadminnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);

    if ($courseadminnode) {
        $url = new moodle_url('/local/gamification/index.php', ['courseid' => $courseid]);

        $gamificationnode = $courseadminnode->add(
            get_string('pluginname', 'local_gamification'),
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            'local_gamification',
            new pix_icon('i/settings', '')
        );
    }
}
