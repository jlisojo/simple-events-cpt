<?php
/**
 * Custom admin list columns for events.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Admin_Columns {

    public function __construct() {
        $type = Simple_Events_Helpers::POST_TYPE;
        add_filter('manage_' . $type . '_posts_columns', array($this, 'set_columns'));
        add_action('manage_' . $type . '_posts_custom_column', array($this, 'render_columns'), 10, 2);
        add_filter('manage_edit-' . $type . '_sortable_columns', array($this, 'set_sortable_columns'));
        add_action('pre_get_posts', array($this, 'orderby_columns'));
    }

    /**
     * Insert custom columns after title.
     *
     * @param array $columns Columns.
     * @return array
     */
    public function set_columns($columns) {
        $new_columns = array();

        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;

            if ($key === 'title') {
                $new_columns['se_event_date']     = __('Event Date', 'simple-events');
                $new_columns['se_event_location'] = __('Location', 'simple-events');
                $new_columns['se_event_price']    = __('Price', 'simple-events');
                $new_columns['se_event_status']   = __('Status', 'simple-events');
            }
        }

        return $new_columns;
    }

    /**
     * Render column values.
     *
     * @param string $column  Column key.
     * @param int    $post_id Post ID.
     */
    public function render_columns($column, $post_id) {
        $event = Simple_Events_Helpers::get_event($post_id);

        switch ($column) {
            case 'se_event_date':
                if ($event['date']) {
                    echo esc_html(Simple_Events_Helpers::format_date_range($event['date'], $event['end_date']));
                    if ($event['time']) {
                        echo '<br><small>' . esc_html(Simple_Events_Helpers::format_time_range($event['time'])) . '</small>';
                    }
                } else {
                    echo '—';
                }
                break;

            case 'se_event_location':
                if ($event['location']) {
                    echo esc_html($event['location']);
                    if ($event['city']) {
                        echo '<br><small>' . esc_html($event['city']) . '</small>';
                    }
                } else {
                    echo '—';
                }
                break;

            case 'se_event_price':
                if ($event['is_free']) {
                    echo '<span class="se-status se-status--free">' . esc_html__('FREE', 'simple-events') . '</span>';
                } elseif ($event['price']) {
                    echo esc_html(Simple_Events_Helpers::format_price($event['price']));
                } else {
                    echo '—';
                }
                break;

            case 'se_event_status':
                $check_date = $event['end_date'] ? $event['end_date'] : $event['date'];

                if (!$check_date) {
                    echo '—';
                    break;
                }

                $event_day = strtotime($check_date);
                $today     = strtotime(current_time('Y-m-d'));

                if ($event_day < $today) {
                    echo '<span class="se-status se-status--past">' . esc_html__('Past', 'simple-events') . '</span>';
                } elseif ($event_day === $today) {
                    echo '<span class="se-status se-status--today">' . esc_html__('Today', 'simple-events') . '</span>';
                } else {
                    $days_until = (int) ceil(($event_day - $today) / DAY_IN_SECONDS);
                    if ($days_until <= 7) {
                        printf(
                            '<span class="se-status se-status--soon">%s</span>',
                            esc_html(sprintf(
                                /* translators: %d: number of days */
                                _n('In %d day', 'In %d days', $days_until, 'simple-events'),
                                $days_until
                            ))
                        );
                    } else {
                        echo '<span class="se-status se-status--upcoming">' . esc_html__('Upcoming', 'simple-events') . '</span>';
                    }
                }
                break;
        }
    }

    /**
     * Make the date column sortable.
     *
     * @param array $columns Columns.
     * @return array
     */
    public function set_sortable_columns($columns) {
        $columns['se_event_date'] = 'se_event_date';
        return $columns;
    }

    /**
     * Sort the admin list by event date.
     *
     * @param WP_Query $query Query.
     */
    public function orderby_columns($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== Simple_Events_Helpers::POST_TYPE) {
            return;
        }

        $orderby = $query->get('orderby');

        if ($orderby === 'se_event_date' || empty($orderby)) {
            $query->set('meta_key', '_se_event_date');
            $query->set('orderby', 'meta_value');

            if (empty($orderby)) {
                $query->set('order', 'ASC');
            }
        }
    }
}
