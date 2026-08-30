<?php
/**
 * Event card used by the archive, taxonomy views, and shortcode.
 *
 * @var int   $post_id
 * @var array $event
 * @var array $args
 */

if (!defined('ABSPATH')) {
    exit;
}

$event          = isset($event) ? $event : Simple_Events_Helpers::get_event($post_id);
$args           = isset($args) ? $args : array();
$is_past        = !empty($args['is_past']);
$show_register  = !isset($args['show_register']) || $args['show_register'];
$date_display   = Simple_Events_Helpers::format_date_range($event['date']);
$time_display   = Simple_Events_Helpers::format_time_range($event['time']);
$location       = Simple_Events_Helpers::format_location($event);
$card_classes   = 'se-card';

if ($is_past) {
    $card_classes .= ' se-card--past';
}
?>
<article class="<?php echo esc_attr($card_classes); ?>">
    <a class="se-card__link" href="<?php echo esc_url(get_permalink($post_id)); ?>">
        <div class="se-card__image">
            <?php if ($is_past) : ?>
                <span class="se-badge se-badge--past"><?php esc_html_e('Past', 'simple-events'); ?></span>
            <?php elseif ($event['is_free']) : ?>
                <span class="se-badge se-badge--free"><?php esc_html_e('Free', 'simple-events'); ?></span>
            <?php endif; ?>

            <?php if (has_post_thumbnail($post_id)) : ?>
                <?php echo get_the_post_thumbnail($post_id, 'medium', array('alt' => esc_attr(get_the_title($post_id)))); ?>
            <?php else : ?>
                <div class="se-card__placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>

        <div class="se-card__body">
            <h3 class="se-card__title"><?php echo esc_html(get_the_title($post_id)); ?></h3>
            <div class="se-card__meta">
                <?php if ($date_display) : ?>
                    <span class="se-card__date"><?php echo esc_html($date_display); ?></span>
                <?php endif; ?>
                <?php if ($time_display) : ?>
                    <span class="se-card__time"><?php echo esc_html($time_display); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($location) : ?>
                <p class="se-card__location"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
            <?php if ($event['short_description']) : ?>
                <p class="se-card__excerpt"><?php echo esc_html(wp_trim_words($event['short_description'], 20)); ?></p>
            <?php endif; ?>
        </div>
    </a>

    <div class="se-card__actions">
        <a class="se-button se-button--secondary" href="<?php echo esc_url(get_permalink($post_id)); ?>">
            <?php echo $is_past ? esc_html__('View Details', 'simple-events') : esc_html__('Learn More', 'simple-events'); ?>
        </a>
        <?php if ($show_register && !$is_past && !empty($event['registration_link'])) : ?>
            <a class="se-button se-button--primary" href="<?php echo esc_url($event['registration_link']); ?>" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Register', 'simple-events'); ?>
            </a>
        <?php endif; ?>
    </div>
</article>
