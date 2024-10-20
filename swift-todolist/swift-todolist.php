<?php
/*
Plugin Name: Swift Todo List
Description: A simple supercharged todo list plugin with create, update, and remove functionality.
Version: 0.1.1
Author: Shayan Ghiaseddin
Website: sghiaseddin.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('SWIFT_TODOLIST_BASE_SLUG', 'swift_todolist');
define('SWIFT_TODOLIST_BASE_URL', site_url('/' . SWIFT_TODOLIST_BASE_SLUG)); // Define the base URL of the Todo List page
define('SWIFT_TODOLIST_VERSION', '0.1.1'); 

// Include required files
require_once plugin_dir_path(__FILE__) . 'activate.php';
require_once plugin_dir_path(__FILE__) . 'include/class-swift-todolist.php';
require_once plugin_dir_path(__FILE__) . 'include/class-swift-ajax-handler.php';

// Initialize the plugin.
new SWIFT_TodoList();

?>