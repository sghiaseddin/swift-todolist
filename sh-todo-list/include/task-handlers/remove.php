<?php
/**
 * SH_Remove_Task
 * 
 * Handles removing tasks from the database.
 */
class SH_Remove_Task {
    public function __construct() {
        add_action('wp_ajax_remove_task', [$this, 'remove_task_callback']);
        add_action('wp_ajax_nopriv_remove_task', [$this, 'remove_task_callback']);
    }

    public function remove_task_callback() {
        $task_id = intval($_POST['task_id']);
        $current_user = wp_get_current_user();

        $removed = $this->remove_task($current_user->ID, $task_id);

        if ($removed) {
            wp_send_json_success(['message' => 'Task removed successfully!']);
        } else {
            wp_send_json_error(['message' => 'Failed to remove task.']);
        }

        wp_die();
    }

    private function remove_task($user_id, $post_id) {
        global $wpdb;

        $task = $this->get_task($user_id, $post_id);
        if (!$task) {
            return false;
        }

        $deleted = $wpdb->delete(
            "{$wpdb->prefix}posts",
            ['ID' => $post_id, 'post_author' => $user_id, 'post_type' => 'task'],
            ['%d', '%d', '%s']
        );

        return $deleted;
    }

    private function get_task($user_id, $post_id) {
        global $wpdb;

        $task = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT p.ID AS id, p.post_title AS title, p.post_content AS content
                FROM {$wpdb->prefix}posts AS p 
                WHERE p.post_author = %d
                AND p.ID = %d
                AND p.post_status = 'publish'
                AND p.post_type = 'task'
                ORDER BY p.post_modified DESC;",
                $user_id, $post_id
            ),
            ARRAY_A
        );

        return $task;
    }
}