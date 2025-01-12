<?php

/**
 * Settings for the Gamification plugin.
 *
 * @package   local_gamification
 * @copyright 2025 Eugene Bachura
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_gamification', get_string('pluginname', 'local_gamification'));

    $settings->add(new admin_setting_configtext(
        'local_gamification/points_task',
        get_string('points_task', 'local_gamification'),
        get_string('points_task_desc', 'local_gamification'),
        10,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'local_gamification/points_forum',
        get_string('points_forum', 'local_gamification'),
        get_string('points_forum_desc', 'local_gamification'),
        5,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'local_gamification/points_quiz',
        get_string('points_quiz', 'local_gamification'),
        get_string('points_quiz_desc', 'local_gamification'),
        15,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_gamification/show_leaderboard',
        get_string('show_leaderboard', 'local_gamification'),
        get_string('show_leaderboard_desc', 'local_gamification'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_gamification/show_badges',
        get_string('show_badges', 'local_gamification'),
        get_string('show_badges_desc', 'local_gamification'),
        1
    ));

    $ADMIN->add('localplugins', $settings);
}
