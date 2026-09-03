<?php
/**
 * [simple_events] shortcode.
 *
 * Usage:
 * [simple_events]
 * [simple_events count="6" title="Upcoming Events" show_button="true"]
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Shortcodes {

    public function __construct() {
        add_shortcode('simple_events', array($this, 'render'));
    }

    /**
     * Render the events grid.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'count'       => 3,
            'title'       => '',
            'show_button' => 'true',
            'button_text' => __('View All Events', 'simple-events-cpt'),
            'button_url'  => '',
        ), $atts, 'simple_events');

        $assets = new Simple_Events_Assets();
        $assets->enqueue();

        $query = new WP_Query(Simple_Events_Helpers::upcoming_query_args(array(
            'posts_per_page' => absint($atts['count']),
        )));

        $title       = sanitize_text_field($atts['title']);
        $show_button = filter_var($atts['show_button'], FILTER_VALIDATE_BOOLEAN);
        $button_text = sanitize_text_field($atts['button_text']);
        $button_url  = $atts['button_url'] ? esc_url($atts['button_url']) : Simple_Events_Helpers::archive_url();

        ob_start();
        ?>
        <div class="se-shortcode">
            <?php if ($title) : ?>
                <h2 class="se-shortcode__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <div class="se-grid">
                <?php if ($query->have_posts()) : ?>
                    <?php
                    while ($query->have_posts()) :
                        $query->the_post();
                        Simple_Events_Helpers::render_card(get_the_ID());
                    endwhile;
                    wp_reset_postdata();
                    ?>
                <?php else : ?>
                    <p class="se-empty"><?php esc_html_e('No upcoming events found.', 'simple-events-cpt'); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($show_button) : ?>
                <a class="se-button se-button--outline se-shortcode__all" href="<?php echo esc_url($button_url); ?>">
                    <?php echo esc_html($button_text); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php

        return ob_get_clean();
    }
}
