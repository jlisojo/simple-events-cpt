<?php
/**
 * Settings API page for archive slug, pagination, and display options.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Settings {

    const OPTION_KEY = 'simple_events_settings';

    /**
     * @var Simple_Events_Settings|null
     */
    private static $instance = null;

    /**
     * Singleton accessor.
     *
     * @return Simple_Events_Settings
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action('admin_init', array($this, 'register'));
        add_action('admin_menu', array($this, 'add_page'));
        add_action('init', array($this, 'maybe_flush_rewrites'), 99);
    }

    /**
     * Default option values.
     *
     * @return array
     */
    public static function defaults() {
        return array(
            'slug'             => 'events',
            'per_page'         => 12,
            'currency_symbol'  => '$',
            'date_format'      => '',
        );
    }

    /**
     * Get a setting.
     *
     * @param string $key     Option key.
     * @param mixed  $default Fallback.
     * @return mixed
     */
    public static function get($key, $default = null) {
        $options  = get_option(self::OPTION_KEY, array());
        $defaults = self::defaults();

        if (isset($options[$key]) && $options[$key] !== '') {
            return $options[$key];
        }

        if (null !== $default) {
            return $default;
        }

        return isset($defaults[$key]) ? $defaults[$key] : '';
    }

    /**
     * Register settings, section, and fields.
     */
    public function register() {
        register_setting('simple_events', self::OPTION_KEY, array(
            'type'              => 'array',
            'sanitize_callback' => array($this, 'sanitize'),
            'default'           => self::defaults(),
        ));

        add_settings_section(
            'simple_events_general',
            __('General', 'simple-events-cpt'),
            '__return_false',
            'simple-events-cpt'
        );

        add_settings_field(
            'slug',
            __('Archive slug', 'simple-events-cpt'),
            array($this, 'render_slug_field'),
            'simple-events-cpt',
            'simple_events_general'
        );

        add_settings_field(
            'per_page',
            __('Events per page', 'simple-events-cpt'),
            array($this, 'render_per_page_field'),
            'simple-events-cpt',
            'simple_events_general'
        );

        add_settings_field(
            'currency_symbol',
            __('Currency symbol', 'simple-events-cpt'),
            array($this, 'render_currency_field'),
            'simple-events-cpt',
            'simple_events_general'
        );

        add_settings_field(
            'date_format',
            __('Date format', 'simple-events-cpt'),
            array($this, 'render_date_format_field'),
            'simple-events-cpt',
            'simple_events_general'
        );
    }

    /**
     * Add submenu under Events.
     */
    public function add_page() {
        add_submenu_page(
            'edit.php?post_type=' . Simple_Events_Helpers::POST_TYPE,
            __('Event Settings', 'simple-events-cpt'),
            __('Settings', 'simple-events-cpt'),
            'manage_options',
            'simple-events-settings',
            array($this, 'render_page')
        );
    }

    /**
     * Sanitize settings and flag rewrite flush when the slug changes.
     *
     * @param array $input Raw input.
     * @return array
     */
    public function sanitize($input) {
        $output   = self::defaults();
        $previous = get_option(self::OPTION_KEY, array());

        $slug = isset($input['slug']) ? sanitize_title($input['slug']) : 'events';
        $output['slug'] = $slug ? $slug : 'events';

        $per_page = isset($input['per_page']) ? absint($input['per_page']) : 12;
        $output['per_page'] = min(50, max(1, $per_page));

        $symbol = isset($input['currency_symbol']) ? sanitize_text_field($input['currency_symbol']) : '$';
        $output['currency_symbol'] = $symbol !== '' ? substr($symbol, 0, 5) : '$';

        $output['date_format'] = isset($input['date_format']) ? sanitize_text_field($input['date_format']) : '';

        $old_slug = isset($previous['slug']) ? $previous['slug'] : 'events';
        if ($old_slug !== $output['slug']) {
            update_option('simple_events_flush_rewrite', '1');
        }

        return $output;
    }

    /**
     * Flush rewrite rules after a slug change.
     */
    public function maybe_flush_rewrites() {
        if (get_option('simple_events_flush_rewrite') === '1') {
            flush_rewrite_rules();
            delete_option('simple_events_flush_rewrite');
        }
    }

    /**
     * Settings page markup.
     */
    public function render_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Event Settings', 'simple-events-cpt'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('simple_events');
                do_settings_sections('simple-events-cpt');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_slug_field() {
        $value = self::get('slug', 'events');
        ?>
        <input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[slug]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description">
            <?php
            printf(
                /* translators: %s: example archive URL */
                esc_html__('Used in event permalinks and the archive URL, e.g. %s', 'simple-events-cpt'),
                esc_html(home_url('/' . $value . '/sample-event/'))
            );
            ?>
        </p>
        <?php
    }

    public function render_per_page_field() {
        $value = (int) self::get('per_page', 12);
        ?>
        <input type="number" min="1" max="50" name="<?php echo esc_attr(self::OPTION_KEY); ?>[per_page]" value="<?php echo esc_attr($value); ?>" class="small-text" />
        <?php
    }

    public function render_currency_field() {
        $value = self::get('currency_symbol', '$');
        ?>
        <input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[currency_symbol]" value="<?php echo esc_attr($value); ?>" class="small-text" maxlength="5" />
        <?php
    }

    public function render_date_format_field() {
        $value = self::get('date_format', '');
        ?>
        <input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[date_format]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr(get_option('date_format')); ?>" />
        <p class="description"><?php esc_html_e('Leave blank to use the WordPress date format from Settings → General.', 'simple-events-cpt'); ?></p>
        <?php
    }
}
