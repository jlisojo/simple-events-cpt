<?php
/**
 * Event post type and taxonomies.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Post_Type {

    public function __construct() {
        add_action('init', array($this, 'register_post_type'), 0);
        add_action('init', array($this, 'register_taxonomies'), 0);
        add_action('pre_get_posts', array($this, 'archive_query'));
    }

    /**
     * Register the events CPT.
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Events', 'Post Type General Name', 'simple-events'),
            'singular_name'         => _x('Event', 'Post Type Singular Name', 'simple-events'),
            'menu_name'             => __('Events', 'simple-events'),
            'name_admin_bar'        => __('Event', 'simple-events'),
            'archives'              => __('Event Archives', 'simple-events'),
            'attributes'            => __('Event Attributes', 'simple-events'),
            'parent_item_colon'     => __('Parent Event:', 'simple-events'),
            'all_items'             => __('All Events', 'simple-events'),
            'add_new_item'          => __('Add New Event', 'simple-events'),
            'add_new'               => __('Add New', 'simple-events'),
            'new_item'              => __('New Event', 'simple-events'),
            'edit_item'             => __('Edit Event', 'simple-events'),
            'update_item'           => __('Update Event', 'simple-events'),
            'view_item'             => __('View Event', 'simple-events'),
            'view_items'            => __('View Events', 'simple-events'),
            'search_items'          => __('Search Events', 'simple-events'),
            'not_found'             => __('No events found', 'simple-events'),
            'not_found_in_trash'    => __('No events found in Trash', 'simple-events'),
            'featured_image'        => __('Event Image', 'simple-events'),
            'set_featured_image'    => __('Set event image', 'simple-events'),
            'remove_featured_image' => __('Remove event image', 'simple-events'),
            'use_featured_image'    => __('Use as event image', 'simple-events'),
            'insert_into_item'      => __('Insert into event', 'simple-events'),
            'uploaded_to_this_item' => __('Uploaded to this event', 'simple-events'),
            'items_list'            => __('Events list', 'simple-events'),
            'items_list_navigation' => __('Events list navigation', 'simple-events'),
            'filter_items_list'     => __('Filter events list', 'simple-events'),
        );

        $args = array(
            'label'               => __('Event', 'simple-events'),
            'description'         => __('Events and activities', 'simple-events'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'taxonomies'          => array(Simple_Events_Helpers::TAX_CATEGORY, Simple_Events_Helpers::TAX_TAG),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-calendar-alt',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => Simple_Events_Helpers::rewrite_slug(),
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
            'rewrite'             => array(
                'slug'       => Simple_Events_Helpers::rewrite_slug(),
                'with_front' => false,
            ),
            'query_var'           => true,
        );

        register_post_type(Simple_Events_Helpers::POST_TYPE, $args);
    }

    /**
     * Register category and tag taxonomies.
     */
    public function register_taxonomies() {
        register_taxonomy(Simple_Events_Helpers::TAX_CATEGORY, array(Simple_Events_Helpers::POST_TYPE), array(
            'labels' => array(
                'name'              => _x('Event Categories', 'taxonomy general name', 'simple-events'),
                'singular_name'     => _x('Event Category', 'taxonomy singular name', 'simple-events'),
                'search_items'      => __('Search Categories', 'simple-events'),
                'all_items'         => __('All Categories', 'simple-events'),
                'parent_item'       => __('Parent Category', 'simple-events'),
                'parent_item_colon' => __('Parent Category:', 'simple-events'),
                'edit_item'         => __('Edit Category', 'simple-events'),
                'update_item'       => __('Update Category', 'simple-events'),
                'add_new_item'      => __('Add New Category', 'simple-events'),
                'new_item_name'     => __('New Category Name', 'simple-events'),
                'menu_name'         => __('Categories', 'simple-events'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'event-category'),
            'show_in_rest'      => true,
        ));

        register_taxonomy(Simple_Events_Helpers::TAX_TAG, array(Simple_Events_Helpers::POST_TYPE), array(
            'labels' => array(
                'name'          => _x('Event Tags', 'taxonomy general name', 'simple-events'),
                'singular_name' => _x('Event Tag', 'taxonomy singular name', 'simple-events'),
                'search_items'  => __('Search Tags', 'simple-events'),
                'all_items'     => __('All Tags', 'simple-events'),
                'edit_item'     => __('Edit Tag', 'simple-events'),
                'update_item'   => __('Update Tag', 'simple-events'),
                'add_new_item'  => __('Add New Tag', 'simple-events'),
                'new_item_name' => __('New Tag Name', 'simple-events'),
                'menu_name'     => __('Tags', 'simple-events'),
            ),
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'event-tag'),
            'show_in_rest'      => true,
        ));
    }

    /**
     * Order the public archive by event date.
     *
     * @param WP_Query $query Query.
     */
    public function archive_query($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if (!$query->is_post_type_archive(Simple_Events_Helpers::POST_TYPE) && !$query->is_tax(array(Simple_Events_Helpers::TAX_CATEGORY, Simple_Events_Helpers::TAX_TAG))) {
            return;
        }

        $query->set('meta_key', '_se_event_date');
        $query->set('orderby', 'meta_value');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', (int) Simple_Events_Settings::get('per_page', 12));
        $query->set('meta_query', array(
            array(
                'key'     => '_se_event_date',
                'value'   => current_time('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATE',
            ),
        ));
    }
}
