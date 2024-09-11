<?php
/**
 * todolist-template.php
 * 
 * Template file for displaying the user's to-do list. Shows tasks with
 * options to create new tasks or view existing tasks in detail.
 */

ob_start(); ?>
    <div class="sh-todo-wrapper"><?php
    $current_user = wp_get_current_user();
    if ( $current_user->ID == 0 ) { ?>
        <p>You don't have access to the page, please log in.</p>
        <a class="button log-in" href="<?= SH_TODOLIST_LOGIN_URL ?>">Login/Register</a><?php
    } else if ( ! $tasks ) { ?>
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
                <div class="sh-task-item" data-task-id="<?= $task['id'] ?>">
                    <div class="sh-task-title"><?= $task['title'] ?></div>
                    <div class="sh-task-desc" data-full-desc="<?= esc_html($task['content']) ?>"><?= esc_html(substr($task['content'], 0, 140) . ( strlen($task['content']) > 140 ? '...' : '')) ?></div>
                    <div class="sh-task-actions">
                        <a href="<?= SH_TODOLIST_BASE_URL ?>/?action=view&id=<?= $task['id'] ?>" title="View Task" class="view-task"><span class="dashicons dashicons-info-outline"></span></a>
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
        ajaxurl = '<?= admin_url( 'admin-ajax.php' ) ?>'; // get ajaxurl
        todolistBase = '<?= SH_TODOLIST_BASE_URL ?>'; // get ajaxurl
    </script><?php

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
