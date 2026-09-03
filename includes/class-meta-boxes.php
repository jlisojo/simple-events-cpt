<?php
/**
 * Event meta boxes and save handlers.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Meta_Boxes {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_' . Simple_Events_Helpers::POST_TYPE, array($this, 'save_meta_boxes'));
    }

    /**
     * Register meta boxes.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'se_event_details',
            __('Event Details', 'simple-events-cpt'),
            array($this, 'render_details_meta_box'),
            Simple_Events_Helpers::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'se_event_pricing',
            __('Pricing Information', 'simple-events-cpt'),
            array($this, 'render_pricing_meta_box'),
            Simple_Events_Helpers::POST_TYPE,
            'side',
            'default'
        );
    }

    /**
     * Details meta box.
     *
     * @param WP_Post $post Post.
     */
    public function render_details_meta_box($post) {
        wp_nonce_field('save_se_event_details', 'se_event_details_nonce');

        $event  = Simple_Events_Helpers::get_event($post->ID);
        $states = Simple_Events_Helpers::us_states();
        ?>
        <div class="se-event-meta">
            <div class="se-event-meta__row">
                <div class="se-event-meta__field se-event-meta__field--full">
                    <label for="se_event_short_description"><?php esc_html_e('Short description for cards', 'simple-events-cpt'); ?></label>
                    <textarea id="se_event_short_description" name="se_event_short_description" maxlength="200" placeholder="<?php esc_attr_e('Brief description for event cards (100–150 characters)', 'simple-events-cpt'); ?>"><?php echo esc_textarea($event['short_description']); ?></textarea>
                    <p class="description"><?php esc_html_e('Shown on archive cards and the shortcode grid.', 'simple-events-cpt'); ?></p>
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field">
                    <label for="se_event_date"><?php esc_html_e('Start date', 'simple-events-cpt'); ?> <span class="required">*</span></label>
                    <input type="date" id="se_event_date" name="se_event_date" value="<?php echo esc_attr($event['date']); ?>" required />
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_end_date"><?php esc_html_e('End date (optional)', 'simple-events-cpt'); ?></label>
                    <input type="date" id="se_event_end_date" name="se_event_end_date" value="<?php echo esc_attr($event['end_date']); ?>" />
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field">
                    <label for="se_event_time"><?php esc_html_e('Start time', 'simple-events-cpt'); ?></label>
                    <input type="time" id="se_event_time" name="se_event_time" value="<?php echo esc_attr($event['time']); ?>" />
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_end_time"><?php esc_html_e('End time', 'simple-events-cpt'); ?></label>
                    <input type="time" id="se_event_end_time" name="se_event_end_time" value="<?php echo esc_attr($event['end_time']); ?>" />
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field se-event-meta__field--full">
                    <label for="se_event_location"><?php esc_html_e('Venue / location name', 'simple-events-cpt'); ?> <span class="required">*</span></label>
                    <input type="text" id="se_event_location" name="se_event_location" value="<?php echo esc_attr($event['location']); ?>" required />
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field se-event-meta__field--full">
                    <label for="se_event_address"><?php esc_html_e('Street address', 'simple-events-cpt'); ?></label>
                    <input type="text" id="se_event_address" name="se_event_address" value="<?php echo esc_attr($event['address']); ?>" />
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field">
                    <label for="se_event_city"><?php esc_html_e('City', 'simple-events-cpt'); ?></label>
                    <input type="text" id="se_event_city" name="se_event_city" value="<?php echo esc_attr($event['city']); ?>" />
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_state"><?php esc_html_e('State', 'simple-events-cpt'); ?></label>
                    <select id="se_event_state" name="se_event_state">
                        <option value=""><?php esc_html_e('Select state', 'simple-events-cpt'); ?></option>
                        <?php foreach ($states as $code => $label) : ?>
                            <option value="<?php echo esc_attr($code); ?>" <?php selected($event['state'], $code); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_zip"><?php esc_html_e('ZIP code', 'simple-events-cpt'); ?></label>
                    <input type="text" id="se_event_zip" name="se_event_zip" value="<?php echo esc_attr($event['zip']); ?>" />
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field">
                    <label for="se_event_phone"><?php esc_html_e('Contact phone', 'simple-events-cpt'); ?></label>
                    <input type="tel" id="se_event_phone" name="se_event_phone" value="<?php echo esc_attr($event['phone']); ?>" />
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_email"><?php esc_html_e('Contact email', 'simple-events-cpt'); ?></label>
                    <input type="email" id="se_event_email" name="se_event_email" value="<?php echo esc_attr($event['email']); ?>" />
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field">
                    <label for="se_event_website"><?php esc_html_e('Event website', 'simple-events-cpt'); ?></label>
                    <input type="url" id="se_event_website" name="se_event_website" value="<?php echo esc_attr($event['website']); ?>" placeholder="https://" />
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_registration_link"><?php esc_html_e('Registration / ticket URL', 'simple-events-cpt'); ?></label>
                    <input type="url" id="se_event_registration_link" name="se_event_registration_link" value="<?php echo esc_attr($event['registration_link']); ?>" placeholder="https://" />
                    <p class="description"><?php esc_html_e('Opens in a new tab from event cards and the single event page.', 'simple-events-cpt'); ?></p>
                </div>
            </div>

            <div class="se-event-meta__row">
                <div class="se-event-meta__field">
                    <label for="se_event_age_range"><?php esc_html_e('Age range', 'simple-events-cpt'); ?></label>
                    <input type="text" id="se_event_age_range" name="se_event_age_range" value="<?php echo esc_attr($event['age_range']); ?>" placeholder="<?php esc_attr_e('e.g., 5–12 years', 'simple-events-cpt'); ?>" />
                </div>
                <div class="se-event-meta__field">
                    <label for="se_event_capacity"><?php esc_html_e('Capacity / availability', 'simple-events-cpt'); ?></label>
                    <input type="text" id="se_event_capacity" name="se_event_capacity" value="<?php echo esc_attr($event['capacity']); ?>" placeholder="<?php esc_attr_e('e.g., Limited, 50 spots', 'simple-events-cpt'); ?>" />
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Pricing meta box.
     *
     * @param WP_Post $post Post.
     */
    public function render_pricing_meta_box($post) {
        $event = Simple_Events_Helpers::get_event($post->ID);
        ?>
        <div class="se-event-pricing">
            <p>
                <label>
                    <input type="checkbox" id="se_event_is_free" name="se_event_is_free" value="1" <?php checked($event['is_free']); ?> />
                    <?php esc_html_e('This is a free event', 'simple-events-cpt'); ?>
                </label>
            </p>
            <p>
                <label for="se_event_price"><?php esc_html_e('General admission', 'simple-events-cpt'); ?></label>
                <input type="text" id="se_event_price" name="se_event_price" value="<?php echo esc_attr($event['price']); ?>" placeholder="25" />
            </p>
            <p>
                <label for="se_event_price_child"><?php esc_html_e('Child price', 'simple-events-cpt'); ?></label>
                <input type="text" id="se_event_price_child" name="se_event_price_child" value="<?php echo esc_attr($event['price_child']); ?>" placeholder="15" />
            </p>
            <p>
                <label for="se_event_price_adult"><?php esc_html_e('Adult price', 'simple-events-cpt'); ?></label>
                <input type="text" id="se_event_price_adult" name="se_event_price_adult" value="<?php echo esc_attr($event['price_adult']); ?>" placeholder="25" />
            </p>
        </div>
        <?php
    }

    /**
     * Persist meta on save.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['se_event_details_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['se_event_details_nonce'])), 'save_se_event_details')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $text_fields = array(
            'date',
            'end_date',
            'time',
            'end_time',
            'location',
            'address',
            'city',
            'state',
            'zip',
            'phone',
            'age_range',
            'capacity',
            'price',
            'price_child',
            'price_adult',
        );

        foreach ($text_fields as $field) {
            $key = 'se_event_' . $field;
            if (isset($_POST[$key])) {
                update_post_meta($post_id, '_se_event_' . $field, sanitize_text_field(wp_unslash($_POST[$key])));
            }
        }

        if (isset($_POST['se_event_short_description'])) {
            update_post_meta($post_id, '_se_event_short_description', sanitize_textarea_field(wp_unslash($_POST['se_event_short_description'])));
        }

        if (isset($_POST['se_event_email'])) {
            update_post_meta($post_id, '_se_event_email', sanitize_email(wp_unslash($_POST['se_event_email'])));
        }

        foreach (array('website', 'registration_link') as $url_field) {
            $key = 'se_event_' . $url_field;
            if (isset($_POST[$key])) {
                update_post_meta($post_id, '_se_event_' . $url_field, esc_url_raw(wp_unslash($_POST[$key])));
            }
        }

        $is_free = isset($_POST['se_event_is_free']) ? '1' : '0';
        update_post_meta($post_id, '_se_event_is_free', $is_free);
    }
}
