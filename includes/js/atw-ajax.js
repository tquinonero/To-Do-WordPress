jQuery(document).ready(function($) {
    // Add new task
    $('.atw-add-task-form').on('submit', function(e) {
        e.preventDefault();
        var taskContent = $(this).find('input[name="new_task"]').val();
        if (taskContent) {
            $.ajax({
                url: atw_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'atw_add_task',
                    task: taskContent,
                    nonce: atw_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.atw-task-list').append(response.data.html);
                        $('input[name="new_task"]').val('');
                        updateProgress();
                    } else {
                        alert('Error adding task: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred while adding the task.');
                }
            });
        }
    });

    // Toggle task completion
    $(document).on('change', '.atw-task-checkbox', function() {
        var taskId = $(this).attr('id').replace('task-', '');
        var isCompleted = $(this).is(':checked');
        var $taskItem = $(this).closest('.atw-task-item');
        
        $.ajax({
            url: atw_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'atw_toggle_task',
                task_id: taskId,
                completed: isCompleted,
                nonce: atw_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (isCompleted) {
                        $taskItem.addClass('completed');
                    } else {
                        $taskItem.removeClass('completed');
                    }
                    updateProgress();
                }
            }
        });
    });

    // Remove task
    $(document).on('click', '.atw-remove-task', function() {
        var taskId = $(this).data('id');
        var $taskItem = $(this).closest('.atw-task-item');
        
        $.ajax({
            url: atw_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'atw_remove_task',
                task_id: taskId,
                nonce: atw_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    $taskItem.fadeOut(300, function() {
                        $(this).remove();
                        updateProgress();
                    });
                } else {
                    alert('Error removing task: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while removing the task.');
            }
        });
    });

    function updateProgress() {
        var total = $('.atw-task-item').length;
        var completed = $('.atw-task-checkbox:checked').length;
        var percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        $('.atw-progress').css('width', percentage + '%');
        $('.atw-progress-bar + p').text(completed + ' / ' + total + ' tasks completed');
    }
});
