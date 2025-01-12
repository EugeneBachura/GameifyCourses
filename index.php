<?php

/**
 * Gamification dashboard.
 *
 * @package   local_gamification
 */

require_once(__DIR__ . '/../../config.php');

use local_gamification\output\renderer;

$timeframe = optional_param('timeframe', 'alltime', PARAM_ALPHANUMEXT);
$courseid  = optional_param('courseid', null, PARAM_INT);

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

global $USER, $CFG, $DB;

$points = $DB->get_field('local_gamification_points', 'points', [
    'userid'   => $USER->id,
    'courseid' => $courseid,
]) ?: 0;

$level = floor(sqrt($points));
$currentLevelPoints = pow($level, 2);
$nextLevelPoints    = pow($level + 1, 2);
$progress           = $points - $currentLevelPoints;
$progressNeeded     = $nextLevelPoints - $currentLevelPoints;
$progressPercent    = $progressNeeded > 0 ? ($progress / $progressNeeded) * 100 : 100;

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
        'name'        => $badge->name,
        'description' => $badge->description,
        'imageurl'    => $badgeimageurl,
    ];
}

$timeconditions = '';
$params = ['courseid' => $courseid];

if ($timeframe === 'week') {
    $timeconditions     = 'AND p.timemodified >= :weekstart';
    $params['weekstart'] = strtotime('-1 week');
} elseif ($timeframe === 'month') {
    $timeconditions      = 'AND p.timemodified >= :monthstart';
    $params['monthstart'] = strtotime('-1 month');
}

$sql = "SELECT u.id, u.firstname, u.lastname, p.points
          FROM {user} u
          JOIN {local_gamification_points} p ON u.id = p.userid
         WHERE p.courseid = :courseid
               $timeconditions
      ORDER BY p.points DESC
       LIMIT 20";
$leaderboardrecords = $DB->get_records_sql($sql, $params);

$leaderboardData = [];
$userInTop20 = false;

foreach ($leaderboardrecords as $index => $record) {
    $leaderboardData[] = [
        'id'     => $record->id,
        'name'   => $record->firstname . ' ' . $record->lastname,
        'points' => $record->points,
    ];
    if ((int)$record->id === (int)$USER->id) {
        $userInTop20 = true;
    }
}

$userRank = null;
if (!$userInTop20) {
    $sql = "SELECT COUNT(*) + 1 AS userrank
              FROM {local_gamification_points} p
             WHERE p.courseid = :courseid
                   {$timeconditions}
               AND p.points > (
                   SELECT points
                     FROM {local_gamification_points}
                    WHERE userid = :userid
                      AND courseid = :courseid2
               )";

    $params['userid']    = $USER->id;
    $params['courseid2'] = $courseid;
    $userRank = $DB->get_field_sql($sql, $params);

    $leaderboardData[] = [
        'id'     => $USER->id,
        'rank'   => (int)$userRank,
        'name'   => fullname($USER) . ' (' . get_string('yourplace', 'local_gamification') . ')',
        'points' => $points,
    ];
}

$data = [
    'avatar'       => new moodle_url('/user/pix.php/' . $USER->id . '/f1.jpg'),
    'fullname'     => fullname($USER),
    'status_icon'  => $USER->lastaccess > time() - 300
        ? '<span class="status online"></span>'
        : '<span class="status offline"></span>',
    'level'        => $level,
    'points'       => $points,
    'progress'     => $progressPercent,
    'progress_text' => "{$progress} / {$progressNeeded}",
    'badges'       => $badges,
    'leaderboard'  => $leaderboardData,
    'courseid'     => $courseid,
    'timeframe'    => $timeframe,
    'currentuserid' => $USER->id,
];

$renderer = $PAGE->get_renderer('local_gamification');

echo $OUTPUT->header();
echo $renderer->render_dashboard($data);
echo $OUTPUT->footer();
