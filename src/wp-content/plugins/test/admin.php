<?php

class testargo22Admin
{
    public static function init()
    {
        add_action('admin_init', ['testargo22Admin', 'settings_init']);

        add_action('admin_menu', ['testargo22Admin', 'options_page']);
    }

    public static function settings_init()
    {
        register_setting('testargo22', 'testargo22_options');

        add_settings_section(
            'testargo22_section_developers',
            __('Set your defaults...', 'testargo22'),
            ['testargo22Admin', 'section_developers_callback'],
            'testargo22'
        );

        add_settings_field(
            'testargo22_field_display_as',
            __('Display', 'testargo22'),
            ['testargo22Admin', 'field_display'],
            'testargo22',
            'testargo22_section_developers',
            [
                'label_for' => 'testargo22_field_display',
                'class' => 'testargo22_row',
            ]
        );

        add_settings_field(
            'testargo22_field_date_format', // As of WP 4.6 this value is used only internally.
            // Use $args' label_for to populate the id inside the callback.
            __('Date format', 'testargo22'),
            ['testargo22Admin', 'field_date_format'],
            'testargo22',
            'testargo22_section_developers',
            [
                'label_for' => 'testargo22_field_date_format',
                'class' => 'testargo22_row',
            ]
        );
    }

    public static function section_developers_callback($args)
    {
        ?>
        <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Koho chleba jíš, toho píseň zpívej.', 'testargo22'); ?></p>
        <?php
    }

    public static function field_display($args)
    {
        $options = get_option('testargo22_options');
        ?>
        <select
            id="<?php echo esc_attr($args['label_for']); ?>"
            name="testargo22_options[<?php echo esc_attr($args['label_for']); ?>]">
            <option value="tiles" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'tiles', false)) : (''); ?>>
                <?php esc_html_e('As tiles', 'testargo22'); ?>
            </option>
            <option value="table" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'table', false)) : (''); ?>>
                <?php esc_html_e('As table', 'testargo22'); ?>
            </option>
        </select>
        <p class="description">
            <?php esc_html_e('This is how you display.', 'testargo22'); ?>
        </p>
        <?php
    }

    public static function field_date_format($args)
    {
        $options = get_option('testargo22_options');
        ?>

        <input
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="testargo22_options[<?php echo esc_attr($args['label_for']); ?>]"
                value="<?php echo isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?>"
        >

        <p class="description">
            <?php esc_html_e('This is how you display date.', 'testargo22'); ?>
        </p>
        <?php
    }

    public static function options_page()
    {
        add_menu_page(
            'Test A22',
            'Test A22 Options',
            'manage_options',
            'testargo22',
            ['testargo22Admin', 'options_page_html'],
        );
    }

    public static function options_page_html()
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        if (isset($_GET['settings-updated'])) {
            add_settings_error('testargo22_messages', 'testargo22_message', __('Settings Saved', 'testargo22'), 'updated');
        }

        settings_errors('testargo22_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('testargo22');
        do_settings_sections('testargo22');
        submit_button('Save Settings');
        ?>
            </form>
        </div>
        <?php
    }
}
