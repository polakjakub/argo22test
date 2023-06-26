<?php

/**
 * Plugin na přijímací test
 *
 * @package           TestArgo22
 * @author            Jakub Polák
 * @copyright         2023
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Test pro Argo 22
 * Description:       Tento plugin...
 * Author:            Jakub Polák
 * Author URI:        https://jakubpolak.cz
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

define('TESTARGO22_SHORTCODE', 'testargo22');

class testargo22
{

    protected static function getEvents($from_date = null, $to_date = null)
    {
        $date_query = [];
        if ($from_date) {
            $date_query['after'] = date('Y-m-d', strtotime($from_date) - 1);
        }
        if ($to_date) {
            $date_query['before'] = date('Y-m-d', strtotime($to_date) + 86400);
        }
        $args = array(
            'numberposts' => 5,
            'post_type' => 'flamingo_inbound',
            'order' => 'date',
            'orderby' => 'DESC',
            'date_query'  => $date_query,
            'post_type' => 'event',
        );

        $events = get_posts($args);

        return $events;
    }

    public static function eventsShortcode($atts)
    {
        $attributes = shortcode_atts(array(
            'format' => 'tiles',
            'date_format' => 'F j, Y',
            'from_date' => null,
            'to_date' => null,
            'meta_key' => 'date_at',
            'orderby' => 'meta_value',
            'order' => 'DESC'
        ), $atts);


        // chtěl jsem použít get_query_var(), ale nějak mě to zlobilo p5id8v8n9 hodnot to WP_Query
        $from_date = filter_input(INPUT_GET, 'from_date');
        $to_date = filter_input(INPUT_GET, 'to_date');
        $format = filter_input(INPUT_GET, 'format');

        if ($from_date && preg_match('/\d{4}\-\d{2}\-\d{2}/', $from_date)) {
            $attributes['from_date'] = $from_date;
        }
        if ($to_date && preg_match('/\d{4}\-\d{2}\-\d{2}/', $to_date)) {
            $attributes['to_date'] = $to_date;
        }
        if ($format) {
            $attributes['format'] = $format;
        }

        // Get events based on the shortcode parameters
        $events = self::getEvents($attributes['from_date'], $attributes['to_date']);

        // Generate HTML for displaying events
        $output = self::getHead();
        $output .= self::getForm($attributes);
        if (count($events) === 0) {
            $output .= self::getEmpty();
        } elseif ($attributes['format'] === 'table') {
            $output .= self::outputAsTable($events, $attributes['date_format']);
        } else {
            $output .= self::outputAsTiles($events, $attributes['date_format']);
        }

        return $output;
    }

    protected static function outputAsTable($events, $date_format = 'D')
    {
        $output = '<table>';
        $output .= '<thead><tr><th>Datum</th><th>Název</th><th>Lokalita</th></tr></thead>';
        foreach ($events as $event) {
            $date = wp_date($date_format, strtotime($event->date_at));
            $output .= "<tr><td>{$date}</td><td>{$event->post_title}</td><td>{$event->location}</td><td>{}</td></tr>\n";

        }
        $output .= '</table>';

        return $output;
    }

    protected static function outputAsTiles($events, $date_format = 'j.n.Y')
    {
        $output = '<div class="event-listing">';
        foreach ($events as $event) {
            $date = wp_date($date_format, strtotime($event->date_at));
            $output .= '<div class="event">';
            $output .= '<h3>' . $event->post_title . '</h3>';
            $output .= '<p>Datum: ' . $date . '</p>';
            $output .= '<p>Lokalita: ' . $event->location . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    }

    protected static function getEmpty()
    {
        return "<p>Nyní zde nejsou žádné akce k zobrazení.</p>";
    }

    protected static function getHead()
    {
        $output = '<style>.testable th, td {padding: 15px;border: 1px solid black;}</style>';

        return $output;
    }

    protected static function getForm($attributes)
    {
        $output = '<form class="testable">';
        $output .= '<label>Od: <input type="date" name="from_date" value="' . $attributes['from_date'] . '"></label>';
        $output .= '<label>Do: <input type="date" name="to_date" value="' . $attributes['to_date'] . '"></label>';
        foreach (['table' => 'tabulka', 'tiles' => 'dlaždice', ] as $value => $label) {
            $selected = $value === $attributes['format'] ? ' checked' : '';
            $output .= '<label><input type="radio" name="format" value="' . $value . '"' . $selected . '>' . $label . '</label>';
        }
        $output .= '<input type="submit">';
        $output .= '</form>';

        return $output;
    }
}


add_shortcode(TESTARGO22_SHORTCODE, ['testargo22', 'eventsShortcode']);

if ( is_admin() ) {
    require __DIR__ . '/admin.php';
    testargo22Admin::init();
}