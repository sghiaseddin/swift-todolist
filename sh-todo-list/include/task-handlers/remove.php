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
        // Verify the nonce
        if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce( sanitize_key( wp_unslash($_POST['_wpnonce']) ), 'sh_todolist_nonce') ) {
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
            wp_send_json_success(['message' => 'Task removed successfully!']);
        } else {
            wp_send_json_error(['message' => 'Failed to remove task.']);
        }

        wp_die();
    }

    private function remove_task($user_id, $post_id) {
        $task = $this->get_task($user_id, $post_id);
        if (!$task) {
            return false;
        }
    
        $deleted = wp_delete_post($post_id, true);
        return $deleted ? true : false;
    }
    
    private function get_task($user_id, $post_id) {
        $args = array(
            'post_type'   => 'task',
            'post_status' => 'publish',
            'author'      => $user_id,
            'p'           => $post_id,
            'orderby'     => 'modified',
            'order'       => 'DESC',
            'posts_per_page' => 1, 
        );
    
        $query = new WP_Query($args);
    
        if ($query->have_posts()) {
            $query->the_post();
            $task = array(
                'id'      => get_the_ID(),
                'title'   => get_the_title(),
                'content' => get_the_content(),
            );
            wp_reset_postdata(); 
            return $task;
        }
    
        return false;
    }
}