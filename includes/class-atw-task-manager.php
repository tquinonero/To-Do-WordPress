<?php

class ATW_Task_Manager {
    private function get_tasks_option_name() {
        $options = get_option('atw_options', array());
        return !empty($options['user_specific_lists']) ? 'atw_tasks_user_' . get_current_user_id() : 'atw_tasks';
    }

    public function get_tasks() {
        return get_option($this->get_tasks_option_name(), array());
    }

    public function add_task($title) {
        $tasks = $this->get_tasks();
        $new_task = array(
            'id' => uniqid(),
            'title' => $title,
            'completed' => false,
            'created_at' => current_time('mysql')
        );
        $tasks[] = $new_task;
        update_option($this->get_tasks_option_name(), $tasks);
        return $new_task;
    }

    public function toggle_task_completion($task_id, $completed) {
        $tasks = $this->get_tasks();
        foreach ($tasks as &$task) {
            if ($task['id'] === $task_id) {
                $task['completed'] = $completed;
                break;
            }
        }
        update_option($this->get_tasks_option_name(), $tasks);
    }

    public function remove_task($task_id) {
        $tasks = $this->get_tasks();
        $tasks = array_filter($tasks, function($task) use ($task_id) {
            return $task['id'] !== $task_id;
        });
        return update_option($this->get_tasks_option_name(), array_values($tasks));
    }
}
