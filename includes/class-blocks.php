<?php
/**
 * Register Gutenberg blocks for Simple Events CPT.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Blocks {

    public function __construct() {
        add_action('init', array($this, 'register_blocks'));
    }

    /**
     * Register Gutenberg blocks.
     */
    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }

        wp_register_script(
            'simple-events-block-grid',
            SIMPLE_EVENTS_PLUGIN_URL . 'assets/js/block-grid.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n'),
            SIMPLE_EVENTS_VERSION
        );

        wp_register_style(
            'simple-events-block-grid-editor',
            SIMPLE_EVENTS_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SIMPLE_EVENTS_VERSION
        );

        register_block_type('simple-events-cpt/event-grid', array(
            'api_version'     => 2,
            'editor_script'   => 'simple-events-block-grid',
            'editor_style'    => 'simple-events-block-grid-editor',
            'style'           => 'simple-events-cpt',
            'attributes'      => array(
                'count'       => array(
                    'type'    => 'number',
                    'default' => 3,
                ),
                'title'       => array(
                    'type'    => 'string',
                    'default' => '',
                ),
                'show_button' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'button_text' => array(
                    'type'    => 'string',
                    'default' => __('View All Events', 'simple-events-cpt'),
                ),
                'button_url'  => array(
                    'type'    => 'string',
                    'default' => '',
                ),
            ),
            'render_callback' => array($this, 'render_event_grid'),
        ));
    }

    /**
     * Server-side render callback for event-grid block.
     *
     * @param array $attributes Block attributes.
     * @return string
     */
    public function render_event_grid($attributes) {
        $shortcode = new Simple_Events_Shortcodes();
        return $shortcode->render(array(
            'count'       => isset($attributes['count']) ? absint($attributes['count']) : 3,
            'title'       => isset($attributes['title']) ? sanitize_text_field($attributes['title']) : '',
            'show_button' => isset($attributes['show_button']) && $attributes['show_button'] ? 'true' : 'false',
            'button_text' => isset($attributes['button_text']) ? sanitize_text_field($attributes['button_text']) : __('View All Events', 'simple-events-cpt'),
            'button_url'  => isset($attributes['button_url']) ? esc_url_raw($attributes['button_url']) : '',
        ));
    }
}
