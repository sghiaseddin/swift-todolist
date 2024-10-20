<?php
/**
 * SWIFT_Remove_Task
 * 
 * Handles removing tasks from the database.
 */
class SWIFT_Remove_Task {
    public function __construct() {
        add_action('wp_ajax_remove_task', [$this, 'remove_task_callback']);
        add_action('wp_ajax_nopriv_remove_task', [$this, 'remove_task_callback']);
    }

    public function remove_task_callback() {
        // Verify the nonce
        if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce( sanitize_key( wp_unslash($_POST['_wpnonce']) ), 'swift_todolist_nonce') ) {
            wp_send_json_error(['message' => 'Nonce verification failed! ' . sanitize_key( wp_unslash($_POST['_wpnonce']) ) ]);
            wp_die();
        }
        $task_id = intval( isset($_POST['task_id']) ? $_POST['task_id'] : '' );
        $current_user = wp_get_current_user();

        if ( $task_id ) {
            $removed = $this->remove_task($current_user->ID, $task_id);
        } else {
            $removed = false;
        }

        if ($removed) {
            wp_send_json_success(['message' => __('Task removed successfully!', 'swift-todolist')]);
        } else {
            wp_send_json_error(['message' => __('Failed to remove task.', 'swift-todolist')]);
        }

        wp_die();
    }

    private function remove_task($user_id, $post_id) {
        $swift_todolist = new SWIFT_TodoList;
        $task = $swift_todolist->get_task($user_id, $post_id);
        if (!$task) {
            return false;
        }
    
        $deleted = wp_delete_post($post_id, true);
        return $deleted ? true : false;
    }
}