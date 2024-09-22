<?php
/**
 * SH_Create_Task
 * 
 * Handles the creation of new tasks.
 */
class SH_Create_Task {
    public function __construct() {
        add_action('wp_ajax_create_task', [$this, 'create_task_callback']);
        add_action('wp_ajax_nopriv_create_task', [$this, 'create_task_callback']);
    }

    public function create_task_callback() {
        // Verify the nonce
        if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce( sanitize_key( wp_unslash($_POST['_wpnonce']) ), 'sh_todolist_nonce') ) {
            wp_send_json_error(['message' => 'Nonce verification failed! ' . sanitize_key( wp_unslash($_POST['_wpnonce']) ) ]);
            wp_die();
        }
        $current_user = wp_get_current_user();
        $title = isset($_POST['title']) ? sanitize_text_field( wp_unslash($_POST['title']) ) : '';
        $content = isset($_POST['content']) ? sanitize_textarea_field( wp_unslash($_POST['content']) ) : ' ';
        $new_task_id = $this->create_task($current_user->ID, $title, $content);

        if ($new_task_id) {
            wp_send_json_success(['message' => 'Task created successfully!', 'task_id' => $new_task_id]);
        } else {
            wp_send_json_error(['message' => 'Failed to create task.']);
        }

        wp_die();
    }

    private function create_task($user_id, $title, $content) {
        $new_task = wp_insert_post([
            'post_title'   => wp_strip_all_tags($title),
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'task',
            'post_author'  => $user_id,
        ]);

        return $new_task;
    }
}