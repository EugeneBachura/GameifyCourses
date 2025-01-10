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

        $output .= html_writer::start_div('user-dashboard');
        $output .= html_writer::start_div('user-info');
        $output .= html_writer::start_div('user-details');
        $output .= html_writer::div(
            html_writer::empty_tag('img', [
                'src' => $data['avatar'],
                'class' => 'circle-avatar'
            ]),
            'avatar'
        );
        $output .= html_writer::tag('h3', $data['fullname'] . $data['status_icon'], ['class' => 'user-name']);
        $output .= html_writer::end_div();
        $output .= html_writer::tag(
            'p',
            get_string('level', 'local_gamification') . ': ' . $data['level'] .
                ' (' . $data['points'] . ' ' . get_string('points', 'local_gamification') . ')',
            ['class' => 'user-level']
        );
        $output .= html_writer::end_div();

        $output .= html_writer::start_div('level-progress');
        $output .= html_writer::start_div('progress');
        $output .= html_writer::div('', 'progress-bar', ['style' => 'width: ' . $data['progress'] . '%;']);
        $output .= html_writer::end_div();
        $output .= html_writer::start_div('progress-points');
        $output .= html_writer::tag('div', $data['progress_text']);
        $output .= html_writer::end_div();
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
        $output .= html_writer::start_div('leaderboard-header');

        $output .= html_writer::tag('h5', get_string('leaderboard', 'local_gamification'), ['class' => 'leaderboard-title']);

        $output .= html_writer::start_div('leaderboard-filter');
        $output .= html_writer::start_tag('form', [
            'method' => 'get',
            'action' => $this->page->url
        ]);
        $output .= html_writer::empty_tag('input', [
            'type'  => 'hidden',
            'name'  => 'courseid',
            'value' => $data['courseid'],
        ]);
        $output .= html_writer::start_tag('select', [
            'name'     => 'timeframe',
            'onchange' => 'this.form.submit()',
            'class'    => 'custom-select'
        ]);
        $output .= html_writer::tag(
            'option',
            get_string('alltime', 'local_gamification'),
            [
                'value'    => 'alltime',
                'selected' => $data['timeframe'] === 'alltime' ? 'selected' : null,
            ]
        );
        $output .= html_writer::tag(
            'option',
            get_string('week', 'local_gamification'),
            [
                'value'    => 'week',
                'selected' => $data['timeframe'] === 'week' ? 'selected' : null,
            ]
        );
        $output .= html_writer::tag(
            'option',
            get_string('month', 'local_gamification'),
            [
                'value'    => 'month',
                'selected' => $data['timeframe'] === 'month' ? 'selected' : null,
            ]
        );
        $output .= html_writer::end_tag('select');
        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_div();

        $output .= html_writer::end_div();

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
            $rank = isset($leader['rank']) ? $leader['rank'] : ($index + 1);
            $rowattrs = [];
            if (!empty($leader['id']) && $leader['id'] == $data['currentuserid']) {
                $rowattrs['style'] = 'font-weight: bold;';
            }

            $output .= html_writer::start_tag('tr', $rowattrs);
            $output .= html_writer::tag('td', $rank);
            $output .= html_writer::tag('td', $leader['name']);
            $output .= html_writer::tag('td', $leader['points']);
            $output .= html_writer::end_tag('tr');
        }

        $output .= html_writer::end_tag('tbody');
        $output .= html_writer::end_tag('table');

        $output .= html_writer::tag(
            'div',
            get_string('filterinactiveusers', 'local_gamification'),
            ['class' => 'filter-note']
        );

        $output .= html_writer::end_div();

        return $output;
    }
}
