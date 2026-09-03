<?php
/**
 * Register and enqueue plugin assets.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Assets {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
    }

    /**
     * Frontend CSS/JS on event views and pages that use the shortcode.
     */
    public function enqueue_frontend() {
        $should_load = is_singular(Simple_Events_Helpers::POST_TYPE)
            || is_post_type_archive(Simple_Events_Helpers::POST_TYPE)
            || is_tax(array(Simple_Events_Helpers::TAX_CATEGORY, Simple_Events_Helpers::TAX_TAG));

        if (!$should_load) {
            $post = get_post();
            if ($post && has_shortcode($post->post_content, 'simple_events')) {
                $should_load = true;
            }
        }

        if ($should_load) {
            $this->enqueue();
        }
    }

    /**
     * Public enqueue used by the shortcode when it renders outside the main content check.
     */
    public function enqueue() {
        wp_enqueue_style(
            'simple-events-cpt',
            SIMPLE_EVENTS_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SIMPLE_EVENTS_VERSION
        );

        wp_enqueue_script(
            'simple-events-cpt',
            SIMPLE_EVENTS_PLUGIN_URL . 'assets/js/frontend.js',
            array(),
            SIMPLE_EVENTS_VERSION,
            true
        );
    }

    /**
     * Admin assets on event screens.
     *
     * @param string $hook Current admin hook.
     */
    public function enqueue_admin($hook) {
        $screen = get_current_screen();

        if (!$screen || $screen->post_type !== Simple_Events_Helpers::POST_TYPE) {
            return;
        }

        wp_enqueue_style(
            'simple-events-admin',
            SIMPLE_EVENTS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SIMPLE_EVENTS_VERSION
        );

        if (in_array($hook, array('post.php', 'post-new.php'), true)) {
            wp_enqueue_script(
                'simple-events-admin',
                SIMPLE_EVENTS_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                SIMPLE_EVENTS_VERSION,
                true
            );
        }
    }
}
