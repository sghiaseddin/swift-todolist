<?php
/**
 * SWIFT_Ajax_Handler
 * 
 * Initializes AJAX handlers for task operations by including and instantiating 
 * individual task handler classes.
 */
class SWIFT_Ajax_Handler {
    public function __construct() {
        require_once plugin_dir_path(__FILE__) . 'task-handlers/create.php';
        require_once plugin_dir_path(__FILE__) . 'task-handlers/update.php';
        require_once plugin_dir_path(__FILE__) . 'task-handlers/remove.php';
        
        new SWIFT_Create_Task();
        new SWIFT_Update_Task();
        new SWIFT_Remove_Task();
    }
}