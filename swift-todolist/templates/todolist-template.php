<?php
/**
 * todolist-template.php
 * 
 * Template file for displaying the user's to-do list. Shows tasks with
 * options to create new tasks or view existing tasks in detail.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
    <div class="swift-todo-wrapper"><?php
    $current_user = wp_get_current_user();
    if ( $current_user->ID == 0 ) { ?>
        <p><?php echo esc_html__("You don't have access to the page, please log in.", 'swift-todolist'); ?></p>
        <a class="button log-in" href="<?php echo esc_url(SWIFT_TODOLIST_LOGIN_URL) ?>"><?php echo esc_html__("Login/Register", 'swift-todolist'); ?></a><?php
    } else if ( empty($tasks) ) { ?>
        <h2><?php echo esc_html__("My Todo List", 'swift-todolist');?></h2>
        <a class="button create-task" href="#"><?php echo esc_html__("Create a Task", 'swift-todolist');?></a>
        <div class="swift-todo-list">
            <div class="swift-todo-list-header">
                <span><?php echo esc_html__("Title", 'swift-todolist');?></span>
                <span><?php echo esc_html__("Description", 'swift-todolist');?></span>
                <span><?php echo esc_html__("Actions", 'swift-todolist');?></span>
            </div>
            <div class="swift-todo-list-container">
                <p class="swift-no-task"><?php echo esc_html__("You don't have any task.", 'swift-todolist'); ?></p>
            </div>
        </div><?php
    } else { ?>
        <h2><?php echo esc_html__("My Todo List", 'swift-todolist');?></h2>
        <a class="button create-task" href="#"><?php echo esc_html__("Create a Task", 'swift-todolist');?></a>
        <div class="swift-todo-list">
            <div class="swift-todo-list-header">
            <span><?php echo esc_html__("Title", 'swift-todolist');?></span>
                <span><?php echo esc_html__("Description", 'swift-todolist');?></span>
                <span><?php echo esc_html__("Actions", 'swift-todolist');?></span>
            </div>
            <div class="swift-todo-list-container"><?php
            foreach ( $tasks as $task ) { ?>
                <div class="swift-task-item" data-task-id="<?php echo esc_html($task->ID) ?>">
                    <div class="swift-task-title"><?php echo esc_html($task->post_title) ?></div>
                    <div class="swift-task-desc" data-full-desc="<?php echo esc_html($task->post_content) ?>">
                        <?php echo esc_html(substr($task->post_content, 0, 140) . ( strlen($task->post_content) > 140 ? '...' : '')) ?>
                    </div>
                    <div class="swift-task-actions">
                        <a href="<?php echo esc_url(SWIFT_TODOLIST_BASE_URL) ?>/?task-action=view&task-id=<?php echo esc_html($task->ID) ?>" title="<?php echo esc_attr__('View Task', 'swift-todolist');?>" class="view-task"><span class="dashicons dashicons-info-outline"></span></a>
                        <a href="#" title="<?php echo esc_attr__('Edit Task', 'swift-todolist');?>" class="edit-task">
                            <span class="dashicons dashicons-edit-large"></span>
                        </a>
                        <a href="#" title="<?php echo esc_attr__('Remove Task', 'swift-todolist');?>" class="remove-task">
                            <span class="dashicons dashicons-trash"></span>
                        </a>
                        <a href="#" title="<?php echo esc_attr__('Update Task', 'swift-todolist');?>" class="update-task" style="display: none;">
                            <span class="dashicons dashicons-yes"></span>
                        </a>
                        <a href="#" title="<?php echo esc_attr__('Cancel', 'swift-todolist');?>" class="cancel-task" style="display: none;">
                            <span class="dashicons dashicons-no"></span>
                        </a>
                    </div>
                </div><?php
            } ?>
            </div>
        </div><?php
    } ?>
    </div><?php
