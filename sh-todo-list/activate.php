<?php
/**
 * Activate.php
 * 
 * Handles tasks during plugin activation, such as setting up roles,
 * capabilities, and any initial settings required for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SH_TodoList_Activator {

    /**
     * Activate the plugin: Check WooCommerce and create the Todo List page.
     */
    public static function activate() {
        self::create_todolist_page();
    }

    /**
     * Create a "Todo List" page with the slug 'todolist' and add the [sh_todolist] shortcode.
     */
    private static function create_todolist_page() {
        $page_slug = 'todolist';

        // Check if a page with the [sh_todolist] shortcode already exists
        $existing_page = get_posts([
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => 1,
            's'           => '[sh_todolist]', // Search for pages containing the shortcode
        ]);

        if (!$existing_page) {
            // No page found with the shortcode, so create one
            $page_id = wp_insert_post([
                'post_title'   => 'Todo List',
                'post_content' => '[sh_todolist]',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => $page_slug, // Sets the page slug to 'todolist'
            ]);

            if ($page_id && !is_wp_error($page_id)) {
                // Page created successfully
                update_option('sh_todolist_page_id', $page_id); // Store page ID in options if needed
            }
        }
    }
}
?>