<?php
/**
 * Plugin Name: Simple Events CPT
 * Description: A lightweight events custom post type with date, location, and pricing fields, theme-overridable templates, Schema.org markup, and a shortcode.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Josh
 * Author URI: https://github.com/jlisojo
 * Text Domain: simple-events
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SIMPLE_EVENTS_VERSION', '1.0.0');
define('SIMPLE_EVENTS_FILE', __FILE__);
define('SIMPLE_EVENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SIMPLE_EVENTS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-helpers.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-settings.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-post-type.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-meta-boxes.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-admin-columns.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-assets.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-routing.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-seo.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-template-loader.php';
require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-shortcodes.php';

/**
 * Bootstrap plugin classes.
 */
function simple_events_init() {
    load_plugin_textdomain('simple-events', false, dirname(plugin_basename(SIMPLE_EVENTS_FILE)) . '/languages');

    Simple_Events_Settings::instance();
    new Simple_Events_Post_Type();
    new Simple_Events_Meta_Boxes();
    new Simple_Events_Admin_Columns();
    new Simple_Events_Assets();
    new Simple_Events_Routing();
    new Simple_Events_SEO();
    new Simple_Events_Template_Loader();
    new Simple_Events_Shortcodes();
}
add_action('plugins_loaded', 'simple_events_init');

/**
 * Register the post type and flush rewrite rules on activation.
 */
function simple_events_activate() {
    require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-helpers.php';
    require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-settings.php';
    require_once SIMPLE_EVENTS_PLUGIN_DIR . 'includes/class-post-type.php';

    $post_type = new Simple_Events_Post_Type();
    $post_type->register_post_type();
    $post_type->register_taxonomies();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'simple_events_activate');

/**
 * Flush rewrite rules on deactivation.
 */
function simple_events_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'simple_events_deactivate');
