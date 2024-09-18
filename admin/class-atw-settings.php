<?php
class ATW_Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_options_page(
            'Admin To-Do List Settings',
            'Admin To-Do List',
            'manage_options',
            'atw-settings',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('atw_options');
        ?>
        <div class="wrap">
            <h1>Admin To-Do List Settings</h1>
            <form method="post" action="options.php">
            <?php
                settings_fields('atw_option_group');
                do_settings_sections('atw-settings');
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'atw_option_group',
            'atw_options',
            array($this, 'sanitize')
        );

        add_settings_section(
            'atw_setting_section',
            'General Settings',
            array($this, 'section_info'),
            'atw-settings'
        );

        add_settings_field(
            'user_specific_lists',
            'User-specific Lists',
            array($this, 'user_specific_lists_callback'),
            'atw-settings',
            'atw_setting_section'
        );

        add_settings_field(
            'enable_notifications',
            'Enable Notifications',
            array($this, 'enable_notifications_callback'),
            'atw-settings',
            'atw_setting_section'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        if(isset($input['user_specific_lists']))
            $new_input['user_specific_lists'] = boolval($input['user_specific_lists']);
        if(isset($input['enable_notifications']))
            $new_input['enable_notifications'] = boolval($input['enable_notifications']);
        return $new_input;
    }

    public function section_info() {
        echo 'Configure the settings for your Admin To-Do List below:';
    }

    public function user_specific_lists_callback() {
        $checked = isset($this->options['user_specific_lists']) ? checked($this->options['user_specific_lists'], true, false) : '';
        echo '<input type="checkbox" id="user_specific_lists" name="atw_options[user_specific_lists]" value="1" ' . $checked . '/>';
        echo '<label for="user_specific_lists"> Enable user-specific to-do lists (if unchecked, all admin users will share the same list)</label>';
    }

    public function enable_notifications_callback() {
        $checked = isset($this->options['enable_notifications']) ? checked($this->options['enable_notifications'], true, false) : '';
        echo '<input type="checkbox" id="enable_notifications" name="atw_options[enable_notifications]" value="1" ' . $checked . '/>';
        echo '<label for="enable_notifications"> Enable notifications for task additions and completions</label>';
    }
}

if (is_admin())
    $atw_settings = new ATW_Settings();
