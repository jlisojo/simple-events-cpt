<?php
/**
 * Load plugin templates with theme override support.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Events_Template_Loader {

    public function __construct() {
        add_filter('template_include', array($this, 'template_include'), 99);
    }

    /**
     * Swap in plugin templates for single events and the events archive.
     *
     * @param string $template Current template.
     * @return string
     */
    public function template_include($template) {
        if (is_singular(Simple_Events_Helpers::POST_TYPE)) {
            return $this->locate('single-se_event.php', $template);
        }

        if (is_post_type_archive(Simple_Events_Helpers::POST_TYPE) || is_tax(array(Simple_Events_Helpers::TAX_CATEGORY, Simple_Events_Helpers::TAX_TAG))) {
            return $this->locate('archive-se_event.php', $template);
        }

        return $template;
    }

    /**
     * Theme template first, then plugin fallback.
     *
     * @param string $filename Template filename.
     * @param string $fallback Original template.
     * @return string
     */
    private function locate($filename, $fallback) {
        $theme = locate_template(array($filename, 'simple-events/' . $filename));
        if ($theme) {
            return $theme;
        }

        $plugin = SIMPLE_EVENTS_PLUGIN_DIR . 'templates/' . $filename;
        if (file_exists($plugin)) {
            return $plugin;
        }

        return $fallback;
    }
}
