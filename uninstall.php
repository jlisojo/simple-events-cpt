<?php
/**
 * Uninstall handler.
 *
 * Removes plugin settings. Event posts, taxonomies, and meta are left in place
 * so content is not destroyed if the plugin is removed accidentally.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('simple_events_settings');
delete_option('simple_events_flush_rewrite');
