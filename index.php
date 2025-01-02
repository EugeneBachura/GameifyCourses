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

$PAGE->requires->css('/local/gamification/styles/styles.css');

global $USER;

$points = $DB->get_field('local_gamification_points', 'points', [
    'userid' => $USER->id,
    'courseid' => $courseid,
]) ?: 0;

$level = floor(sqrt($points));
$currentLevelPoints = pow($level, 2);
$nextLevelPoints = pow($level + 1, 2);
$progress = $points - $currentLevelPoints;
$progressNeeded = $nextLevelPoints - $currentLevelPoints;
$progressPercent = $progressNeeded > 0 ? ($progress / $progressNeeded) * 100 : 100;

global $CFG;
$badges = [];
$sql = "SELECT b.*, bi.dateissued, bi.dateexpire
          FROM {badge} b
     INNER JOIN {badge_issued} bi ON b.id = bi.badgeid
         WHERE bi.userid = :userid";
$params = ['userid' => $USER->id];
$userbadges = $DB->get_records_sql($sql, $params);

$fs = get_file_storage();

foreach ($userbadges as $badge) {
    $context = ($badge->type == 1)
        ? context_system::instance()
        : context_course::instance($badge->courseid);

    $files = $fs->get_area_files($context->id, 'badges', 'badgeimage', $badge->id, false, false);

    $badgeimageurl = null;
    foreach ($files as $file) {
        if (!$file->is_directory()) {
            $url = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
            $badgeimageurl = preg_replace('/\.\w+$/', '', $url->out());
            break;
        }
    }

    $badges[] = [
        'name' => $badge->name,
        'description' => $badge->description,
        'imageurl' => $badgeimageurl,
    ];
}


$sql = "SELECT u.firstname, u.lastname, p.points
          FROM {user} u
          JOIN {local_gamification_points} p ON u.id = p.userid
         WHERE p.courseid = :courseid
      ORDER BY p.points DESC";
$params = ['courseid' => $courseid];
$leaderboard = $DB->get_records_sql($sql, $params);

$leaderboardData = [];
foreach ($leaderboard as $record) {
    $leaderboardData[] = [
        'name' => $record->firstname . ' ' . $record->lastname,
        'points' => $record->points,
    ];
}

$data = [
    'avatar' => new moodle_url('/user/pix.php/' . $USER->id . '/f1.jpg'),
    'fullname' => fullname($USER),
    'status_icon' => $USER->lastaccess > time() - 300 ? '<span class="status online"></span>' : '<span class="status offline"></span>',
    'level' => $level,
    'points' => $points,
    'progress' => $progressPercent,
    'progress_text' => "{$progress} / {$progressNeeded}",
    'badges' => $badges,
    'leaderboard' => $leaderboardData,
];

$renderer = $PAGE->get_renderer('local_gamification');
echo $OUTPUT->header();
echo $renderer->render_dashboard($data);
echo $OUTPUT->footer();
