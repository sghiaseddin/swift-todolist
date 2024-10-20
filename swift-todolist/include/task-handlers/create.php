<?php
/**
 * SWIFT_Create_Task
 * 
 * Handles the creation of new tasks.
 */
class SWIFT_Create_Task {
    public function __construct() {
        add_action('wp_ajax_create_task', [$this, 'create_task_callback']);
        add_action('wp_ajax_nopriv_create_task', [$this, 'create_task_callback']);
    }

    public function create_task_callback() {
        // Verify the nonce
        if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce( sanitize_key( wp_unslash($_POST['_wpnonce']) ), 'swift_todolist_nonce') ) {
            wp_send_json_error(['message' => __('Nonce verification failed!', 'swift-todolist') . ' ' . sanitize_key( wp_unslash($_POST['_wpnonce']) ) ]);
            wp_die();
        }
        $current_user = wp_get_current_user();
        $title = isset($_POST['title']) ? sanitize_text_field( wp_unslash($_POST['title']) ) : '';
        $content = isset($_POST['content']) ? sanitize_textarea_field( wp_unslash($_POST['content']) ) : ' ';
        $new_task_id = $this->create_task($current_user->ID, $title, $content);

        if ($new_task_id) {
            wp_send_json_success(['message' => __('Task created successfully!', 'swift-todolist'), 'task_id' => $new_task_id]);
        } else {
            wp_send_json_error(['message' => __('Failed to create task.', 'swift-todolist')]);
        }

        wp_die();
    }

    private function create_task($user_id, $title, $content) {
        $new_task = wp_insert_post([
            'post_title'   => wp_strip_all_tags($title),
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'swifttask',
            'post_author'  => $user_id,
        ]);

        return $new_task;
    }
}