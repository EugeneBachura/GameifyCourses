<?php

/**
 * Gamification dashboard.
 *
 * @package   local_gamification
 */

require_once(__DIR__ . '/../../config.php');

use local_gamification\output\renderer;

$courseid = optional_param('courseid', null, PARAM_INT);

if (!$courseid) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('missingcourseid', 'local_gamification'), 'error');
    echo $OUTPUT->footer();
    die();
}

if (!$DB->record_exists('course', ['id' => $courseid])) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('invalidcourse', 'local_gamification'), 'error');
    echo $OUTPUT->footer();
    die();
}

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

$PAGE->set_url('/local/gamification/index.php', ['courseid' => $courseid]);
$PAGE->set_course($course);
$PAGE->set_context(context_course::instance($courseid));

$PAGE->set_pagelayout('course');

$PAGE->set_title(get_string('pluginname', 'local_gamification') . ' - ' . $course->fullname);
$PAGE->set_heading($course->fullname);

global $DB, $USER;
$points = $DB->get_field('local_gamification_points', 'points', [
    'userid' => $USER->id,
    'courseid' => $courseid,
]);

echo $OUTPUT->header();
echo $renderer->render_dashboard($data);
echo $OUTPUT->heading(get_string('pluginname', 'local_gamification'), 3);
echo $OUTPUT->box_start();
echo html_writer::tag('p', get_string('yourpoints', 'local_gamification') . ': ' . ($points ?: 0));
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
