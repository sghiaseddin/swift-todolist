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
        // Verify the nonce
        if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce( sanitize_key( wp_unslash($_POST['_wpnonce']) ), 'sh_todolist_nonce') ) {
            wp_send_json_error(['message' => 'Nonce verification failed! ' . sanitize_key( wp_unslash($_POST['_wpnonce']) ) ]);
            wp_die();
        }
        $task_id = isset($_POST['task_id']) ? intval( sanitize_key( wp_unslash($_POST['task_id']) ) ) : '';
        $new_title = isset($_POST['title']) ? sanitize_text_field( wp_unslash($_POST['title']) ) : '';
        $new_desc = isset($_POST['content']) ? sanitize_textarea_field( wp_unslash($_POST['content']) ) : ' ';
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
        $task = $this->get_task($user_id, $post_id);
        if (!$task) {
            return false;
        }
    
        $post_data = array(
            'ID'           => $post_id,
            'post_title'   => wp_strip_all_tags($title),
            'post_content' => $content,
        );
    
        $is_updated = wp_update_post($post_data);
        return $is_updated ? true : false;
    }
}