<?php
/**
 * Optional permalink compatibility for Custom Post Type Permalinks (CPTP).
 *
 * CPTP can set a query var matching the post type slug instead of `name`,
 * which breaks WordPress's is_single() detection and can surface as 404s
 * in SEO plugins. When CPTP is active, intercept the main query and resolve
 * the event by slug before WordPress treats it as a miss.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Routing {

    public function __construct() {
        if ($this->cptp_is_active()) {
            add_filter('posts_pre_query', array($this, 'fix_event_routing'), 10, 2);
        }
    }

    /**
     * Whether Custom Post Type Permalinks is loaded.
     *
     * @return bool
     */
    private function cptp_is_active() {
        return defined('CPTP_VERSION') || class_exists('Custom_Post_Type_Permalinks');
    }

    /**
     * Resolve a CPTP-style event request to a published post.
     *
     * @param array|null $posts Posts or null.
     * @param WP_Query   $query Query.
     * @return array|null
     */
    public function fix_event_routing($posts, $query) {
        if (is_admin() || !$query->is_main_query()) {
            return $posts;
        }

        $slug = $query->get(Simple_Events_Helpers::POST_TYPE);

        if (empty($slug) || !is_string($slug)) {
            return $posts;
        }

        $post = get_page_by_path($slug, OBJECT, Simple_Events_Helpers::POST_TYPE);

        if ($post && $post->post_status === 'publish') {
            $query->is_singular       = true;
            $query->is_single         = true;
            $query->is_404            = false;
            $query->found_posts       = 1;
            $query->max_num_pages     = 1;
            $query->queried_object    = $post;
            $query->queried_object_id = $post->ID;
            $query->set('name', $slug);
            $query->set('p', $post->ID);

            return array($post);
        }

        return $posts;
    }
}
