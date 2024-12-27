<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('gamification', get_string('pluginname', 'gamification'));

    $settings->add(new admin_setting_configtext(
        'local_gamification/points_per_task',
        'Points per task',
        'Number of points awarded per completed task.',
        10
    ));

    $ADMIN->add('localplugins', $settings);
}
