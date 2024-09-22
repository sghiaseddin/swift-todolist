<?php
/*
Plugin Name: SH Todo List
Description: A simple to-do list plugin with create, update, and remove functionality.
Version: 0.0.3
Author: Shayan Ghiaseddin
Website: sghiaseddin.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('SH_TODOLIST_BASE_URL', site_url('/todolist')); // Define the base URL of the Todo List page
define('SH_TODOLIST_VERSION', '0.0.3'); 

// Include required files
require_once plugin_dir_path(__FILE__) . 'activate.php';
require_once plugin_dir_path(__FILE__) . 'include/class-sh-todolist.php';
require_once plugin_dir_path(__FILE__) . 'include/class-sh-ajax-handler.php';

// Initialize the plugin.
new SH_TodoList();

?>