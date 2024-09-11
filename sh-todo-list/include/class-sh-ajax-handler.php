<?php
/**
 * SH_Ajax_Handler
 * 
 * Initializes AJAX handlers for task operations by including and instantiating 
 * individual task handler classes.
 */
class SH_Ajax_Handler {
    public function __construct() {
        require_once plugin_dir_path(__FILE__) . 'task-handlers/create.php';
        require_once plugin_dir_path(__FILE__) . 'task-handlers/update.php';
        require_once plugin_dir_path(__FILE__) . 'task-handlers/remove.php';
        
        new SH_Create_Task();
        new SH_Update_Task();
        new SH_Remove_Task();
    }
}