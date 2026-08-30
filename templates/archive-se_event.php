<?php
/**
 * Events archive template.
 *
 * Copy this file to your theme as `archive-se_event.php` or
 * `simple-events/archive-se_event.php` to override it.
 */

if (!defined('ABSPATH')) {
    exit;
}

$past_query = new WP_Query(Simple_Events_Helpers::past_query_args());

get_header();
?>

<main class="se-archive">
    <div class="se-archive__inner">
        <header class="se-archive__header">
            <h1 class="se-archive__title"><?php post_type_archive_title(); ?></h1>
            <?php if (term_description()) : ?>
                <div class="se-archive__description"><?php echo wp_kses_post(term_description()); ?></div>
            <?php endif; ?>
        </header>

        <div class="se-grid">
            <?php if (have_posts()) : ?>
                <?php
                while (have_posts()) :
                    the_post();
                    Simple_Events_Helpers::render_card(get_the_ID());
                endwhile;
                ?>
            <?php else : ?>
                <p class="se-empty"><?php esc_html_e('No upcoming events found. Check back soon.', 'simple-events'); ?></p>
            <?php endif; ?>
        </div>

        <?php
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => __('← Previous', 'simple-events'),
            'next_text' => __('Next →', 'simple-events'),
        ));
        ?>

        <?php if ($past_query->have_posts()) : ?>
            <section class="se-past">
                <button class="se-past__toggle" type="button" aria-expanded="false" aria-controls="se-past-grid">
                    <h2><?php esc_html_e('Past Events', 'simple-events'); ?></h2>
                    <span class="se-past__icon" aria-hidden="true">▼</span>
                </button>
                <div id="se-past-grid" class="se-grid se-past__grid" hidden>
                    <?php
                    while ($past_query->have_posts()) :
                        $past_query->the_post();
                        Simple_Events_Helpers::render_card(get_the_ID(), array(
                            'is_past'       => true,
                            'show_register' => false,
                        ));
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
