<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ATW_Widget_Display {
    private $task_manager;
    
    public function __construct() {
        $this->task_manager = new ATW_Task_Manager();
    }

    public function display_widget() {
        $tasks = $this->task_manager->get_tasks();
        $total_tasks = count($tasks);
        $completed_tasks = $this->count_completed_tasks($tasks);

        echo '<div class="atw-widget-container">';
        
        // Display progress
        $this->display_progress($completed_tasks, $total_tasks);

        // Display tasks
        $this->display_tasks($tasks);

        // Add new task form
        $this->display_add_task_form();

        echo '</div>';
    }

    private function get_tasks() {
        // Retrieve tasks from database or option
        return get_option('atw_tasks', array());
    }

    private function count_completed_tasks($tasks) {
        return count(array_filter($tasks, function($task) {
            return isset($task['completed']) && $task['completed'];
        }));
    }

    private function display_progress($completed, $total) {
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
        echo '<div class="atw-progress-bar">';
        echo '<div class="atw-progress" style="width: ' . esc_attr($percentage) . '%;"></div>';
        echo '</div>';
        echo '<p>' . esc_html($completed) . ' / ' . esc_html($total) . ' tasks completed</p>';
    }

    private function display_tasks($tasks) {
        echo '<ul class="atw-task-list">';
        foreach ($tasks as $id => $task) {
            $this->display_single_task($id, $task);
        }
        echo '</ul>';
    }

    private function display_single_task($id, $task) {
        $checked = isset($task['completed']) && $task['completed'] ? 'checked' : '';
        $completed_class = isset($task['completed']) && $task['completed'] ? 'completed' : '';
        echo '<li class="atw-task-item ' . $completed_class . '">';
        echo '<input type="checkbox" id="task-' . esc_attr($id) . '" ' . $checked . ' class="atw-task-checkbox">';
        echo '<div class="atw-task-content">';
        echo '<label for="task-' . esc_attr($id) . '">' . esc_html($task['title']) . '</label>';
        echo '<button class="atw-remove-task button button-small" data-id="' . esc_attr($id) . '">Remove</button>';
        echo '</div>';
        echo '</li>';
    }

    private function display_add_task_form() {
        echo '<form class="atw-add-task-form">';
        echo '<input type="text" name="new_task" placeholder="Enter new task">';
        echo '<button type="submit">Add Task</button>';
        echo '</form>';
    }
}
