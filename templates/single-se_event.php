<?php
/**
 * Single event template.
 *
 * Copy this file to your theme as `single-se_event.php` or
 * `simple-events/single-se_event.php` to override it.
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$event   = Simple_Events_Helpers::get_event($post_id);

$date_display = Simple_Events_Helpers::format_date_range($event['date'], $event['end_date']);
$time_display = Simple_Events_Helpers::format_time_range($event['time'], $event['end_time']);
$cta_label    = $event['is_free'] ? __('Register Now', 'simple-events') : __('Register / Buy Tickets', 'simple-events');
$map_query    = trim($event['address'] . ' ' . $event['city'] . ' ' . $event['state'] . ' ' . $event['zip']);

get_header();
?>

<main class="se-single" itemscope itemtype="https://schema.org/Event">
    <div class="se-single__inner">
        <?php while (have_posts()) : the_post(); ?>
            <article class="se-single__article">
                <header class="se-single__header">
                    <h1 class="se-single__title" itemprop="name"><?php the_title(); ?></h1>
                    <?php if ($event['registration_link']) : ?>
                        <a class="se-button se-button--primary se-single__cta-mobile" href="<?php echo esc_url($event['registration_link']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($cta_label); ?>
                        </a>
                    <?php endif; ?>
                </header>

                <div class="se-single__layout">
                    <div class="se-single__main">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="se-single__image">
                                <?php if ($event['is_free']) : ?>
                                    <span class="se-badge se-badge--free"><?php esc_html_e('Free Event', 'simple-events'); ?></span>
                                <?php elseif ($event['price']) : ?>
                                    <span class="se-badge se-badge--price"><?php echo esc_html(sprintf(__('From %s', 'simple-events'), Simple_Events_Helpers::format_price($event['price']))); ?></span>
                                <?php endif; ?>
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="se-single__content" itemprop="description">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <aside class="se-single__sidebar">
                        <?php if ($event['registration_link']) : ?>
                            <div class="se-panel se-panel--cta">
                                <a class="se-button se-button--primary se-button--block" href="<?php echo esc_url($event['registration_link']); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html($cta_label); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="se-panel">
                            <h2><?php esc_html_e('Event Details', 'simple-events'); ?></h2>
                            <?php if ($date_display) : ?>
                                <div class="se-panel__row">
                                    <strong><?php esc_html_e('Date', 'simple-events'); ?></strong>
                                    <span><?php echo esc_html($date_display); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($time_display) : ?>
                                <div class="se-panel__row">
                                    <strong><?php esc_html_e('Time', 'simple-events'); ?></strong>
                                    <span><?php echo esc_html($time_display); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($event['age_range']) : ?>
                                <div class="se-panel__row">
                                    <strong><?php esc_html_e('Ages', 'simple-events'); ?></strong>
                                    <span><?php echo esc_html($event['age_range']); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($event['capacity']) : ?>
                                <div class="se-panel__row">
                                    <strong><?php esc_html_e('Capacity', 'simple-events'); ?></strong>
                                    <span><?php echo esc_html($event['capacity']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($event['location']) : ?>
                            <div class="se-panel">
                                <h2><?php esc_html_e('Location', 'simple-events'); ?></h2>
                                <p class="se-panel__venue"><strong><?php echo esc_html($event['location']); ?></strong></p>
                                <?php if ($event['address']) : ?>
                                    <address>
                                        <?php echo esc_html($event['address']); ?><br>
                                        <?php if ($event['city'] || $event['state'] || $event['zip']) : ?>
                                            <?php echo esc_html(trim($event['city'] . ', ' . $event['state'] . ' ' . $event['zip'], ', ')); ?>
                                        <?php endif; ?>
                                    </address>
                                <?php endif; ?>
                                <?php if ($map_query) : ?>
                                    <a class="se-map-link" href="<?php echo esc_url('https://maps.google.com/?q=' . rawurlencode($map_query)); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php esc_html_e('View on Map', 'simple-events'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$event['is_free'] && ($event['price'] || $event['price_child'] || $event['price_adult'])) : ?>
                            <div class="se-panel">
                                <h2><?php esc_html_e('Pricing', 'simple-events'); ?></h2>
                                <?php if ($event['price']) : ?>
                                    <div class="se-panel__row">
                                        <strong><?php esc_html_e('General', 'simple-events'); ?></strong>
                                        <span><?php echo esc_html(Simple_Events_Helpers::format_price($event['price'])); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($event['price_child']) : ?>
                                    <div class="se-panel__row">
                                        <strong><?php esc_html_e('Child', 'simple-events'); ?></strong>
                                        <span><?php echo esc_html(Simple_Events_Helpers::format_price($event['price_child'])); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($event['price_adult']) : ?>
                                    <div class="se-panel__row">
                                        <strong><?php esc_html_e('Adult', 'simple-events'); ?></strong>
                                        <span><?php echo esc_html(Simple_Events_Helpers::format_price($event['price_adult'])); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($event['phone'] || $event['email'] || $event['website']) : ?>
                            <div class="se-panel">
                                <h2><?php esc_html_e('Contact', 'simple-events'); ?></h2>
                                <?php if ($event['phone']) : ?>
                                    <div class="se-panel__row">
                                        <strong><?php esc_html_e('Phone', 'simple-events'); ?></strong>
                                        <a href="<?php echo esc_url('tel:' . $event['phone']); ?>"><?php echo esc_html($event['phone']); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($event['email']) : ?>
                                    <div class="se-panel__row">
                                        <strong><?php esc_html_e('Email', 'simple-events'); ?></strong>
                                        <a href="<?php echo esc_url('mailto:' . $event['email']); ?>"><?php echo esc_html($event['email']); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($event['website']) : ?>
                                    <div class="se-panel__row">
                                        <strong><?php esc_html_e('Website', 'simple-events'); ?></strong>
                                        <a href="<?php echo esc_url($event['website']); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Visit website', 'simple-events'); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </aside>
                </div>

                <p class="se-single__back">
                    <a href="<?php echo esc_url(Simple_Events_Helpers::archive_url()); ?>">
                        <?php esc_html_e('← Back to all events', 'simple-events'); ?>
                    </a>
                </p>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
