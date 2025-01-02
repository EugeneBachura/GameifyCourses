<?php

namespace local_gamification\output;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use plugin_renderer_base;

class renderer extends plugin_renderer_base
{
    /**
     * Render the user dashboard.
     *
     * @param array $data
     * @return string
     */
    public function render_dashboard($data)
    {
        $output = '';

        $output .= html_writer::start_div('user-info');
        $output .= html_writer::start_div('user-details');
        $output .= html_writer::div(html_writer::empty_tag('img', ['src' => $data['avatar'], 'class' => 'circle-avatar']), 'avatar');
        $output .= html_writer::tag('h3', $data['fullname'] . $data['status_icon'], ['class' => 'user-name']);
        $output .= html_writer::end_div();
        $output .= html_writer::tag('p', get_string('level', 'local_gamification') . ': ' . $data['level'] . ' (' . $data['points'] . ' ' . get_string('points', 'local_gamification') . ')', ['class' => 'user-level']);
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('level-progress');
        $output .= html_writer::start_div('progress');
        $output .= html_writer::div('', 'progress-bar', ['style' => 'width: ' . $data['progress'] . '%;']);
        $output .= html_writer::end_div();
        $output .= html_writer::start_div('progress-points');
        $output .= html_writer::tag('p', $data['progress_text']);
        $output .= html_writer::end_div();
        $output .= html_writer::end_div();

        if (!empty($data['badges'])) {
            $output .= html_writer::start_div('user-badges');
            $output .= html_writer::tag('h5', get_string('yourbadges', 'local_gamification'));

            $output .= html_writer::start_div('badges-block');
            foreach ($data['badges'] as $badge) {
                $output .= html_writer::start_div('badge-container');
                $output .= html_writer::empty_tag('img', [
                    'src'   => $badge['imageurl'],
                    'class' => 'badge-image',
                    'alt'   => htmlspecialchars($badge['name']),
                    'title' => htmlspecialchars($badge['description']),
                ]);
                $output .= html_writer::div(htmlspecialchars($badge['name']), 'badge-name');
                $output .= html_writer::end_div();
            }
            $output .= html_writer::end_div();

            $output .= html_writer::end_div();
        }

        $output .= html_writer::start_div('leaderboard');
        $output .= html_writer::tag('h5', get_string('leaderboard', 'local_gamification'));
        $output .= html_writer::start_tag('table', ['class' => 'generaltable leaderboard-table']);
        $output .= html_writer::start_tag('thead');
        $output .= html_writer::tag(
            'tr',
            html_writer::tag('th', get_string('rank', 'local_gamification')) .
                html_writer::tag('th', get_string('user', 'local_gamification')) .
                html_writer::tag('th', get_string('points', 'local_gamification'))
        );
        $output .= html_writer::end_tag('thead');
        $output .= html_writer::start_tag('tbody');
        foreach ($data['leaderboard'] as $index => $leader) {
            $output .= html_writer::tag(
                'tr',
                html_writer::tag('td', $index + 1) .
                    html_writer::tag('td', $leader['name']) .
                    html_writer::tag('td', $leader['points'])
            );
        }
        $output .= html_writer::end_tag('tbody');
        $output .= html_writer::end_tag('table');
        $output .= html_writer::end_div();

        return $output;
    }
}
