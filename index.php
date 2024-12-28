<?php

/**
 * Gamification dashboard.
 *
 * @package   local_gamification
 */

require_once(__DIR__ . '/../../config.php');

$PAGE->set_url('/local/gamification/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Gamification');
$PAGE->set_heading('Gamification Dashboard');

echo $OUTPUT->header();
echo $OUTPUT->heading('Welcome to Gamification plugin!');
echo $OUTPUT->footer();
