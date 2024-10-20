/**
 * swift-todolist.js
 * 
 * JavaScript file for handling AJAX operations on tasks such as creating,
 * updating, and removing tasks from the to-do list via user interactions.
 * author: Shayan Ghiaseddin
 * website: sghiaseddin.com
 */
jQuery(document).ready(function($) {
    // Function to shorten descriptions
    function shortenDescription(desc) {
        return desc.length > 140 ? desc.substring(0, 140) : desc;
    }

    // Shorten descriptions on page load
    $('.swift-task-desc').each(function() {
        var desc = $(this).text();
        if (desc.length > 140) {
            $(this).addClass('long-desc'); // Add class for styling :after "..."
            $(this).text(shortenDescription(desc));
        }
    });

    
    // Create Task functionality
    $('.swift-todo-wrapper').on('click', '.create-task', function(e) {
        e.preventDefault();

        // Create a new task row at the beginning of the task list
        var newTaskRow = `
            <div class="swift-task-item new-task-item">
                <div class="swift-task-title">
                    <input type="text" class="new-task-title" placeholder="Enter task title">
                </div>
                <div class="swift-task-desc">
                    <textarea class="new-task-desc" placeholder="Enter task description"></textarea>
                </div>
                <div class="swift-task-actions">
                    <a href="#" title="Save Task" class="save-task">
                        <span class="dashicons dashicons-yes"></span>
                    </a>
                    <a href="#" title="Cancel" class="cancel-new-task">
                        <span class="dashicons dashicons-no"></span>
                    </a>
                </div>
            </div>
        `;
        $('.swift-todo-list-container').prepend(newTaskRow);
    });

    // Save New Task functionality
    $('.swift-todo-wrapper').on('click', '.save-task', function(e) {
        e.preventDefault();

        var newTaskItem = $(this).closest('.swift-task-item');
        var newTitle = newTaskItem.find('.new-task-title').val();
        var newDesc = newTaskItem.find('.new-task-desc').val();

        // Basic validation
        if (newTitle.trim() === '' || newDesc.trim() === '') {
            alert('Please enter both title and description.');
            return;
        }

        // AJAX request to create task
        $.ajax({
            url: swiftTodoList.ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'create_task',
                title: newTitle,
                content: newDesc,
                _wpnonce: swiftTodoList.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Replace input fields with the new task display
                    newTaskItem.data('task-id', response.data.task_id);
                    newTaskItem.find('.swift-task-title').text(newTitle);
                    newTaskItem.find('.swift-task-desc').text(shortenDescription(newDesc)).data('full-desc', newDesc);
                    newTaskItem.find('.swift-task-actions').html(`
                        <a href="`+swiftTodoList.swiftTodolistBase+`/?action=view&id=`+response.data.task_id+`" title="View Task" class="view-task"><span class="dashicons dashicons-info-outline"></span></a>
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
                    `);
                    $('.swift-no-task').hide();
                } else {
                    alert('Failed to create task.');
                    newTaskItem.remove(); // Remove the row if failed
                }
            },
            error: function() {
                alert('An error occurred while creating the task.');
                newTaskItem.remove(); // Remove the row if error occurs
            }
        });
    });

    // Cancel New Task functionality
    $('.swift-todo-wrapper').on('click', '.cancel-new-task', function(e) {
        e.preventDefault();

        // Remove the new task row
        $(this).closest('.swift-task-item').remove();
    });


    // Edit Task functionality with event delegation
    $('.swift-todo-wrapper').on('click', '.edit-task', function (e) {
        e.preventDefault();

        var taskItem = $(this).closest('.swift-task-item');
        var taskTitle = taskItem.find('.swift-task-title');
        var taskDesc = taskItem.find('.swift-task-desc');
        var actions = taskItem.find('.swift-task-actions');

        // Replace title and description with input fields
        var currentTitle = taskTitle.text();
        var fullDesc = taskDesc.data('full-desc') || taskDesc.text(); // Use data attribute or current text
        taskDesc.removeClass('long-desc'); // Remove the class that adds "..."

        taskTitle.data('original', currentTitle).html('<input type="text" class="edit-title" value="' + currentTitle + '">');
        taskDesc.html('<textarea class="edit-desc">' + fullDesc + '</textarea>');

        // Show cancel and update buttons, hide others
        actions.find('.edit-task, .remove-task, .view-task').hide();
        actions.find('.update-task, .cancel-task').show();
    });

    // Cancel Task functionality with event delegation
    $('.swift-todo-wrapper').on('click', '.cancel-task', function (e) {
        e.preventDefault();

        var taskItem = $(this).closest('.swift-task-item');
        var taskTitle = taskItem.find('.swift-task-title');
        var taskDesc = taskItem.find('.swift-task-desc');
        var actions = taskItem.find('.swift-task-actions');

        // Restore original title and shortened description
        var originalTitle = taskTitle.data('original');
        var fullDesc = taskDesc.data('full-desc');
        
        taskTitle.text(originalTitle);
        if (fullDesc.length > 140) {
            taskItem.find('.swift-task-desc').addClass('long-desc'); // Class for styling :after "..."
            taskItem.find('.swift-task-desc').data('full-desc', fullDesc).text(shortenDescription(fullDesc));
        } else {
            taskItem.find('.swift-task-desc').data('full-desc', fullDesc).text(fullDesc);
        }

        // Show other actions and hide cancel/update
        actions.find('.edit-task, .remove-task, .view-task').show();
        actions.find('.update-task, .cancel-task').hide();
    });

    // Update Task functionality with event delegation
    $('.swift-todo-wrapper').on('click', '.update-task', function (e) {
        e.preventDefault();

        var taskItem = $(this).closest('.swift-task-item');
        var taskId = taskItem.data('task-id'); // Assuming each task item has a data attribute for the ID
        var newTitle = taskItem.find('.edit-title').val();
        var newDesc = taskItem.find('.edit-desc').val();

        // AJAX request to update task
        $.ajax({
            url: swiftTodoList.ajaxurl,
            type: 'POST',
            data: {
                action: 'update_task',
                task_id: taskId,
                title: newTitle,
                description: newDesc,
                _wpnonce: swiftTodoList.nonce
            },
            success: function(response) {
                if(response.success) {
                    // Update UI with new data
                    taskItem.find('.swift-task-title').text(newTitle);
                    if (newDesc.length > 140) {
                        taskItem.find('.swift-task-desc').addClass('long-desc'); // Class for styling :after "..."
                        taskItem.find('.swift-task-desc').data('full-desc', newDesc).text(shortenDescription(newDesc));
                    } else {
                        taskItem.find('.swift-task-desc').data('full-desc', newDesc).text(newDesc);
                    }

                    // Show other actions and hide cancel/update
                    taskItem.find('.edit-task, .remove-task, .view-task').show();
                    taskItem.find('.update-task, .cancel-task').hide();
                    newDesc = taskItem.find('.swift-task-desc').text();                            

                } else {
                    alert('Failed to update task.');
                }
            },
            error: function() {
                alert('An error occurred while updating the task.');
            }
        });
    });

    // Remove Task functionality with event delegation
    $('.swift-todo-wrapper').on('click', '.remove-task', function (e) {
        e.preventDefault();

        var taskItem = $(this).closest('.swift-task-item');
        var actions = taskItem.find('.swift-task-actions');

        // Hide all action buttons and show confirmation message
        actions.find('.edit-task, .remove-task, .view-task, .update-task, .cancel-task').hide();
        actions.append('<p class="confirm-remove">Sure?</br><a href="#" class="confirm-yes"><span class="dashicons dashicons-yes"></span></a><a href="#" class="confirm-no"><span class="dashicons dashicons-no"></span></a></p>');

        // Handle confirm "Yes"
        actions.find('.confirm-yes').on('click', function(e) {
            e.preventDefault();

            var taskId = taskItem.data('task-id');

            // AJAX request to remove task
            $.ajax({
                url: swiftTodoList.ajaxurl,
                type: 'POST',
                data: {
                    action: 'remove_task',
                    task_id: taskId,
                    _wpnonce: swiftTodoList.nonce
                },
                success: function(response) {
                    if(response.success) {
                        // Remove task from the DOM
                        taskItem.remove();
                    } else {
                        alert('Failed to remove task.');
                        actions.find('.confirm-remove').remove();
                        actions.find('.edit-task, .remove-task, .view-task').show();
                    }
                },
                error: function() {
                    alert('An error occurred while removing the task.');
                    actions.find('.confirm-remove').remove();
                    actions.find('.edit-task, .remove-task, .view-task').show();
                }
            });
        });

        // Handle confirm "No"
        actions.find('.confirm-no').on('click', function(e) {
            e.preventDefault();
            actions.find('.confirm-remove').remove();
            actions.find('.edit-task, .remove-task, .view-task').show();
        });
    });
});

