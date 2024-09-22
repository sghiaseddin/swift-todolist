<?php
/**
 * todolist-template.php
 * 
 * Template file for displaying the user's to-do list. Shows tasks with
 * options to create new tasks or view existing tasks in detail.
 */
?>
    <div class="sh-todo-wrapper"><?php
    $current_user = wp_get_current_user();
    if ( $current_user->ID == 0 ) { ?>
        <p>You don't have access to the page, please log in.</p>
        <a class="button log-in" href="<?php echo esc_html(SH_TODOLIST_LOGIN_URL) ?>">Login/Register</a><?php
    } else if ( empty($tasks) ) { ?>
        <h2>My Todo List</h2>
        <a class="button create-task" href="#">Create a Task</a>
        <div class="sh-todo-list">
            <div class="sh-todo-list-header">
                <span>Title</span>
                <span>Description</span>
                <span>Actions</span>
            </div>
            <div class="sh-todo-list-container">
                <p class="sh-no-task">You don't have any task.</p>
            </div>
        </div><?php
    } else { ?>
        <h2>My Todo List</h2>
        <a class="button create-task" href="#">Create a Task</a>
        <div class="sh-todo-list">
            <div class="sh-todo-list-header">
                <span>Title</span>
                <span>Description</span>
                <span>Actions</span>
            </div>
            <div class="sh-todo-list-container"><?php
            foreach ( $tasks as $task ) { ?>
                <div class="sh-task-item" data-task-id="<?php echo esc_html($task->ID) ?>">
                    <div class="sh-task-title"><?php echo esc_html($task->post_title) ?></div>
                    <div class="sh-task-desc" data-full-desc="<?php echo esc_html($task->post_content) ?>">
                        <?php echo esc_html(substr($task->post_content, 0, 140) . ( strlen($task->post_content) > 140 ? '...' : '')) ?>
                    </div>
                    <div class="sh-task-actions">
                        <a href="<?php echo esc_html(SH_TODOLIST_BASE_URL) ?>/?task-action=view&task-id=<?php echo esc_html($task->ID) ?>" title="View Task" class="view-task"><span class="dashicons dashicons-info-outline"></span></a>
                        <a href="#" title="Edit Task" class="edit-task">
                            <span class="dashicons dashicons-edit-large"></span>
                        </a>
                        <a href="#" title="Remove Task" class="remove-task">
                            <span class="dashicons dashicons-trash"></span>
                        </a>
                        <a href="#" title="Update Task" class="update-task" style="display: none;">
                            <span class="dashicons dashicons-yes"></span>
                        </a>
                        <a href="#" title="Cancel" class="cancel-task" style="display: none;">
                            <span class="dashicons dashicons-no"></span>
                        </a>
                    </div>
                </div><?php
            } ?>
            </div>
        </div><?php
    } ?>
    </div>
    <script>
        ajaxurl = '<?php echo esc_html(admin_url( 'admin-ajax.php' )) ?>'; // get ajaxurl
        todolistBase = '<?php echo esc_html(SH_TODOLIST_BASE_URL) ?>'; // get todolist base URL
    </script>
<?php