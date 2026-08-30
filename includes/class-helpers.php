<?php
/**
 * Shared helpers for the events post type.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Helpers {

    const POST_TYPE = 'se_event';
    const TAX_CATEGORY = 'se_event_category';
    const TAX_TAG = 'se_event_tag';

    /**
     * Event meta field names (stored as _se_event_{key}).
     *
     * @return string[]
     */
    public static function meta_keys() {
        return array(
            'date',
            'end_date',
            'time',
            'end_time',
            'location',
            'address',
            'city',
            'state',
            'zip',
            'phone',
            'email',
            'website',
            'registration_link',
            'age_range',
            'capacity',
            'short_description',
            'price',
            'price_child',
            'price_adult',
            'is_free',
        );
    }

    /**
     * Get a single event meta value.
     *
     * @param int    $post_id Post ID.
     * @param string $key     Field key without prefix.
     * @return mixed
     */
    public static function meta($post_id, $key) {
        return get_post_meta($post_id, '_se_event_' . $key, true);
    }

    /**
     * Get all event fields for a post.
     *
     * @param int $post_id Post ID.
     * @return array
     */
    public static function get_event($post_id) {
        $data = array();

        foreach (self::meta_keys() as $key) {
            $data[$key] = self::meta($post_id, $key);
        }

        $data['is_free'] = ($data['is_free'] === '1') || self::price_is_free($data['price']);

        return $data;
    }

    /**
     * Whether a price value represents a free event.
     *
     * @param string $price Price string.
     * @return bool
     */
    public static function price_is_free($price) {
        $normalized = strtolower(trim((string) $price));
        return $normalized === '0' || $normalized === 'free';
    }

    /**
     * Format a date or date range using plugin/WordPress settings.
     *
     * @param string $start Start date (Y-m-d).
     * @param string $end   Optional end date.
     * @return string
     */
    public static function format_date_range($start, $end = '') {
        if (!$start) {
            return '';
        }

        $format = Simple_Events_Settings::get('date_format');
        if (!$format) {
            $format = get_option('date_format');
        }

        $output = wp_date($format, strtotime($start));

        if ($end && $end !== $start) {
            $output .= ' – ' . wp_date($format, strtotime($end));
        }

        return $output;
    }

    /**
     * Format a time or time range.
     *
     * @param string $start Start time (H:i).
     * @param string $end   Optional end time.
     * @return string
     */
    public static function format_time_range($start, $end = '') {
        if (!$start) {
            return '';
        }

        $format = get_option('time_format');
        $output = wp_date($format, strtotime('1970-01-01 ' . $start));

        if ($end) {
            $output .= ' – ' . wp_date($format, strtotime('1970-01-01 ' . $end));
        }

        return $output;
    }

    /**
     * Format a price with the configured currency symbol.
     *
     * @param string $amount Amount.
     * @return string
     */
    public static function format_price($amount) {
        if ($amount === '' || $amount === null) {
            return '';
        }

        $symbol = Simple_Events_Settings::get('currency_symbol', '$');
        return $symbol . $amount;
    }

    /**
     * Build a location display string.
     *
     * @param array $event Event data.
     * @return string
     */
    public static function format_location($event) {
        $parts = array();

        if (!empty($event['location'])) {
            $parts[] = $event['location'];
        }

        $city_state = array_filter(array(
            isset($event['city']) ? $event['city'] : '',
            isset($event['state']) ? $event['state'] : '',
        ));

        if ($city_state) {
            $parts[] = implode(', ', $city_state);
        }

        return implode(', ', $parts);
    }

    /**
     * Archive URL for the events post type.
     *
     * @return string
     */
    public static function archive_url() {
        $link = get_post_type_archive_link(self::POST_TYPE);
        return $link ? $link : home_url('/' . Simple_Events_Settings::get('slug', 'events') . '/');
    }

    /**
     * Rewrite slug from settings, filterable.
     *
     * @return string
     */
    public static function rewrite_slug() {
        $slug = Simple_Events_Settings::get('slug', 'events');
        $slug = $slug ? $slug : 'events';
        return apply_filters('simple_events_rewrite_slug', $slug);
    }

    /**
     * Query args for upcoming events.
     *
     * @param array $args Overrides.
     * @return array
     */
    public static function upcoming_query_args($args = array()) {
        $defaults = array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => (int) Simple_Events_Settings::get('per_page', 12),
            'meta_key'       => '_se_event_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'     => '_se_event_date',
                    'value'   => current_time('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ),
            ),
        );

        return wp_parse_args($args, $defaults);
    }

    /**
     * Query args for past events.
     *
     * @param array $args Overrides.
     * @return array
     */
    public static function past_query_args($args = array()) {
        $defaults = array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => (int) Simple_Events_Settings::get('per_page', 12),
            'meta_key'       => '_se_event_date',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => '_se_event_date',
                    'value'   => current_time('Y-m-d'),
                    'compare' => '<',
                    'type'    => 'DATE',
                ),
            ),
        );

        return wp_parse_args($args, $defaults);
    }

    /**
     * Render an event card.
     *
     * @param int   $post_id Post ID.
     * @param array $args    Display args.
     */
    public static function render_card($post_id, $args = array()) {
        $args = wp_parse_args($args, array(
            'show_register' => true,
            'is_past'       => false,
        ));

        $event = self::get_event($post_id);
        $template = SIMPLE_EVENTS_PLUGIN_DIR . 'templates/parts/event-card.php';

        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * US states for the location dropdown.
     *
     * @return array
     */
    public static function us_states() {
        return array(
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        );
    }
}
