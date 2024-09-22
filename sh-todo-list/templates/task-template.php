<?php 
/**
 * task-template.php
 * 
 * Template file for displaying a single task's details, including title,
 * content, and management actions like editing or deleting the task.
 */

?>
<div class="sh-todo-wrapper" data-task-id="<?php echo $task['id'] ? esc_html($task['id']) : '' ?>"><?php
$current_user = wp_get_current_user();
if ( $current_user->ID == 0 ) { ?>
    <p>You don't have access to the page, please log in.</p>
    <a class="button log-in" href="<?php echo esc_html(SH_TODOLIST_LOGIN_URL) ?>">Login/Register</a><?php
} else if ( ! $task ) { ?>
    <p>There is no task here.</p>
    <a class="button" href="/prototype/todo/my-account/todolist/">Go back to your Todo List</a><?php
} else { ?>
    <h2><?php echo esc_html($task['title']) ?></h2>
    <div class="sh-task-actions"></div>
    <div class="sh-task-container"><?php echo esc_html($task['content']) ?></div>
    <a href="<?php echo esc_url(SH_TODOLIST_BASE_URL) ?>" class="button go-back-todolist-page" style="margin-top: 40px;">Go Back to your Todo List</a><?php
} ?>
</div><?php

