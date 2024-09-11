<?php
/**
 * SH_Update_Task
 * 
 * Handles updating of existing tasks.
 */
class SH_Update_Task {
    public function __construct() {
        add_action('wp_ajax_update_task', [$this, 'update_task_callback']);
        add_action('wp_ajax_nopriv_update_task', [$this, 'update_task_callback']);
    }

    public function update_task_callback() {
        $task_id = intval($_POST['task_id']);
        $new_title = sanitize_text_field($_POST['title']);
        $new_desc = sanitize_textarea_field($_POST['description']);
        $current_user = wp_get_current_user();

        $updated = $this->update_task($current_user->ID, $task_id, $new_title, $new_desc);

        if ($updated) {
            wp_send_json_success(['message' => 'Task updated successfully!']);
        } else {
            wp_send_json_error(['message' => 'Failed to update task.']);
        }

        wp_die();
    }

    private function update_task($user_id, $post_id, $title, $content) {
        global $wpdb;

        $is_updated = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}posts 
                SET post_title = %s, post_content = %s 
                WHERE ID = %d
                AND post_author = %d
                AND post_type = 'task'",
                $title, $content, $post_id, $user_id
            )
        );

        return $is_updated;
    }
}