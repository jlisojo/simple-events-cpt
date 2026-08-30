<?php
/**
 * Canonical URL and title fixes, plus Schema.org JSON-LD.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_SEO {

    public function __construct() {
        add_filter('rank_math/frontend/canonical', array($this, 'fix_canonical_url'));
        add_filter('rank_math/frontend/title', array($this, 'fix_page_title'));
        add_filter('wpseo_canonical', array($this, 'fix_canonical_url'));
        add_filter('wpseo_title', array($this, 'fix_page_title'));
        add_filter('document_title_parts', array($this, 'fix_document_title'));
        add_filter('pre_get_document_title', array($this, 'pre_get_title'));
        add_action('wp_head', array($this, 'output_schema'), 5);
    }

    /**
     * Canonical URL for published events.
     *
     * @param string $canonical Canonical URL.
     * @return string
     */
    public function fix_canonical_url($canonical) {
        if (is_singular(Simple_Events_Helpers::POST_TYPE)) {
            return get_permalink();
        }

        return $canonical;
    }

    /**
     * Avoid 404 titles on valid event permalinks.
     *
     * @param string $title Title.
     * @return string
     */
    public function fix_page_title($title) {
        if (!is_singular(Simple_Events_Helpers::POST_TYPE)) {
            return $title;
        }

        if (is_string($title) && (false !== strpos($title, 'Page not found') || false !== strpos($title, '404'))) {
            return get_the_title() . ' - ' . get_bloginfo('name');
        }

        return $title;
    }

    /**
     * Document title parts backup.
     *
     * @param array $title Title parts.
     * @return array
     */
    public function fix_document_title($title) {
        if (is_singular(Simple_Events_Helpers::POST_TYPE)) {
            $title['title'] = get_the_title();

            if (isset($title['tagline']) && false !== strpos($title['tagline'], '404')) {
                unset($title['tagline']);
            }
        }

        return $title;
    }

    /**
     * Early title override if WordPress still thinks the request is a 404.
     *
     * @param string $title Title.
     * @return string
     */
    public function pre_get_title($title) {
        if (is_singular(Simple_Events_Helpers::POST_TYPE) && is_404()) {
            return get_the_title() . ' - ' . get_bloginfo('name');
        }

        return $title;
    }

    /**
     * Print JSON-LD for single events and the events archive.
     */
    public function output_schema() {
        if (is_singular(Simple_Events_Helpers::POST_TYPE)) {
            $this->print_json_ld($this->event_schema(get_the_ID()));
            return;
        }

        if (is_post_type_archive(Simple_Events_Helpers::POST_TYPE)) {
            $this->print_json_ld($this->archive_schema());
        }
    }

    /**
     * Schema.org Event object.
     *
     * @param int $post_id Post ID.
     * @return array
     */
    public function event_schema($post_id) {
        $event = Simple_Events_Helpers::get_event($post_id);

        $description = $event['short_description'];
        if (!$description) {
            $description = wp_trim_words(get_the_excerpt($post_id), 30, '…');
        }
        if (!$description) {
            $description = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), 30, '…');
        }

        $schema = array(
            '@context'            => 'https://schema.org',
            '@type'               => 'Event',
            'name'                => get_the_title($post_id),
            'description'         => $description,
            'url'                 => get_permalink($post_id),
            'eventStatus'         => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'organizer'           => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
                'url'   => home_url(),
            ),
        );

        if ($event['date']) {
            $start = $event['date'] . ' ' . ($event['time'] ? $event['time'] : '00:00:00');
            $schema['startDate'] = gmdate('c', strtotime($start));

            $end_date = $event['end_date'] ? $event['end_date'] : $event['date'];
            $end_time = $event['end_time'] ? $event['end_time'] : ($event['time'] ? gmdate('H:i:s', strtotime($event['time']) + 7200) : '23:59:59');
            $schema['endDate'] = gmdate('c', strtotime($end_date . ' ' . $end_time));
        }

        $image = get_the_post_thumbnail_url($post_id, 'large');
        if ($image) {
            $schema['image'] = array($image);
        }

        if ($event['location'] || $event['address']) {
            $schema['location'] = array(
                '@type'   => 'Place',
                'name'    => $event['location'] ? $event['location'] : __('Event Venue', 'simple-events'),
                'address' => array(
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => $event['address'],
                    'addressLocality' => $event['city'],
                    'addressRegion'   => $event['state'],
                    'postalCode'      => $event['zip'],
                    'addressCountry'  => 'US',
                ),
            );
        }

        $offer_url = $event['registration_link'] ? $event['registration_link'] : get_permalink($post_id);

        if ($event['is_free']) {
            $schema['offers'] = array(
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock',
                'url'           => $offer_url,
            );
        } elseif ($event['price']) {
            $numeric = preg_replace('/[^0-9.]/', '', $event['price']);
            $schema['offers'] = array(
                '@type'         => 'Offer',
                'price'         => $numeric ? $numeric : '0',
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock',
                'url'           => $offer_url,
            );
        }

        return $schema;
    }

    /**
     * Schema.org ItemList for the archive.
     *
     * @return array
     */
    private function archive_schema() {
        global $wp_query;

        $items    = array();
        $position = 1;

        if ($wp_query && !empty($wp_query->posts)) {
            foreach ($wp_query->posts as $post) {
                $event_schema = $this->event_schema($post->ID);
                unset($event_schema['@context']);

                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'item'     => $event_schema,
                );
                $position++;
            }
        }

        return array(
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'name'            => __('Upcoming Events', 'simple-events'),
            'description'     => sprintf(
                /* translators: %s: site name */
                __('Upcoming events from %s', 'simple-events'),
                get_bloginfo('name')
            ),
            'url'             => Simple_Events_Helpers::archive_url(),
            'numberOfItems'   => count($items),
            'itemListElement' => $items,
        );
    }

    /**
     * Echo a JSON-LD script tag.
     *
     * @param array $schema Schema array.
     */
    private function print_json_ld($schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n</script>\n";
    }
}
