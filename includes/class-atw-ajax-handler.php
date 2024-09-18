<?php
class ATW_Ajax_Handler {

    private $task_manager;

    public function __construct() {
        $this->task_manager = new ATW_Task_Manager();
        add_action('wp_ajax_atw_add_task', array($this, 'add_task'));
        add_action('wp_ajax_atw_toggle_task', array($this, 'toggle_task'));
        add_action('wp_ajax_atw_remove_task', array($this, 'remove_task'));
    }

    public function add_task() {
        check_ajax_referer('atw_ajax_nonce', 'nonce');

        $task = sanitize_text_field($_POST['task']);
        if (empty($task)) {
            wp_send_json_error('Task cannot be empty');
        }

        $new_task = $this->task_manager->add_task($task);

        $completed_class = $new_task['completed'] ? 'completed' : '';
        $html = sprintf(
            '<li class="atw-task-item %s">
                <input type="checkbox" id="task-%s" class="atw-task-checkbox" %s>
                <div class="atw-task-content">
                    <label for="task-%s">%s</label>
                    <button class="atw-remove-task button button-small" data-id="%s">Remove</button>
                </div>
            </li>',
            $completed_class,
            esc_attr($new_task['id']),
            $new_task['completed'] ? 'checked' : '',
            esc_attr($new_task['id']),
            esc_html($new_task['title']),
            esc_attr($new_task['id'])
        );

        wp_send_json_success(array('html' => $html));
    }

    public function toggle_task() {
        check_ajax_referer('atw_ajax_nonce', 'nonce');

        $task_id = sanitize_text_field($_POST['task_id']);
        $completed = $_POST['completed'] === 'true';

        $this->task_manager->toggle_task_completion($task_id, $completed);
        wp_send_json_success();
    }

    public function remove_task() {
        check_ajax_referer('atw_ajax_nonce', 'nonce');

        $task_id = sanitize_text_field($_POST['task_id']);

        if ($this->task_manager->remove_task($task_id)) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to remove task');
        }
    }
}

// Do not instantiate the class here
// new ATW_Ajax_Handler();
