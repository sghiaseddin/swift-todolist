<?php 
/**
 * task-template.php
 * 
 * Template file for displaying a single task's details, including title,
 * content, and management actions like editing or deleting the task.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<div class="swift-todo-wrapper" data-task-id="<?php echo $task['id'] ? esc_html($task['id']) : '' ?>"><?php
$current_user = wp_get_current_user();
if ( $current_user->ID == 0 ) { ?>
    <p><?php echo esc_html__("You don't have access to the page, please log in.", 'swift-todolist');?></p>
    <a class="button log-in" href="<?php echo esc_url(SWIFT_TODOLIST_LOGIN_URL) ?>"><?php echo esc_html__('Login/Register', 'swift-todolist');?></a><?php
} else if ( ! $task ) { ?>
    <p><?php echo esc_html__('There is no task here.', 'swift-todolist');?></p>
    <a class="button" href="<?php echo esc_url(SWIFT_TODOLIST_BASE_URL) ?>"><?php echo esc_html__('Go back to your Todo List', 'swift-todolist');?></a><?php
} else { ?>
    <h2><?php echo esc_html($task['title']) ?></h2>
    <div class="swift-task-actions"></div>
    <div class="swift-task-container"><?php echo esc_html($task['content']) ?></div>
    <a href="<?php echo esc_url(SWIFT_TODOLIST_BASE_URL) ?>" class="button go-back-todolist-page" style="margin-top: 40px;"><?php echo esc_html__('Go Back to your Todo List', 'swift-todolist');?></a><?php
} ?>
</div><?php

