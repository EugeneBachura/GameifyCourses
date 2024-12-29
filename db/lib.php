<?php

defined('MOODLE_INTERNAL') || die();

function local_gamification_extend_navigation_course($navigation, $course, $context)
{
    local_gamification_extend_settings_navigation($navigation, $context);
}
