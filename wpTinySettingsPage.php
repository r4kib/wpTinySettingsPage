<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 05-Apr-19
 * Time: 12:53 AM
 */

if (class_exists('WPTinySettingsPage')) {
    return false;
}

class WPTinySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $pageTitle;
    private $menuTitle;
    private $menuSlug;
    private $optionName;
    private $optionGroup;
    private $settings;

    /**
     * TinySettingsPage constructor.
     * @param $pageTitle string
     * @param $menuTitle string
     * @param $menuSlug string
     * @param $optionName string
     * @param $settings array
     */
    public function __construct($pageTitle, $menuTitle, $menuSlug, $optionName, $settings)
    {
        $this->pageTitle = $pageTitle;
        $this->menuTitle = $menuTitle;
        $this->menuSlug = $menuSlug;
        $this->optionName = $optionName;
        $this->optionGroup = $optionName . '_group';
        $this->settings = $settings;
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_plugin_page'));
            add_action('admin_init', array($this, 'page_init'));
        }
    }


    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            $this->pageTitle,
            $this->menuTitle,
            'manage_options',
            $this->menuSlug,
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option($this->optionName);
//        var_dump($this->options);
        ?>
        <div class="wrap">
            <h1><?php echo $this->pageTitle; ?></h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields($this->optionGroup);
                do_settings_sections($this->menuSlug);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        $settingFields = array();
        foreach ($this->settings as $sectionId => $sectionArgs) {
            foreach ($sectionArgs['fields'] as $fieldId => $fieldArgs) {
                $settingFields[$fieldId] = $fieldArgs;
            }
        }
        register_setting(
            $this->optionGroup, // Option group
            $this->optionName, // Option name
            function ($input) use ($settingFields) {
                return $this->sanitize($input, $settingFields);
            } // Sanitize
        );

        foreach ($this->settings as $sectionId => $sectionArgs) {
            add_settings_section(
                $sectionId, // ID
                $sectionArgs['title'], // Title
                $sectionArgs['callback'], // Callback
                $this->menuSlug // Page
            );

            foreach ($sectionArgs['fields'] as $fieldId => $fieldArgs) {
                add_settings_field(
                    $fieldId, // ID
                    $fieldArgs['title'], // Title
                    function () use ($fieldId, $fieldArgs) {
                        $this->callbackGenerate($fieldId, $fieldArgs);
                    }, // Callback
                    $this->menuSlug, // Page
                    $sectionId // Section
                );
            }

        }


    }

    public function callbackGenerate($fieldId, $fieldArgs)
    {
        switch ($fieldArgs['type']) {
            case 'checkboxes':
                $this->checkboxes($fieldId, $fieldArgs);
        }
    }

    public function checkboxes($fieldId, $fieldArgs)
    {

        foreach ($fieldArgs['options'] as $value => $label) {
            $checked = in_array($value,$this->options[$fieldId]) ? 'checked' : '';
            $this->multipleInputTagGen('checkbox', $fieldId, $value, $checked, $label . "<br>");
        }
    }

    public function singularInputTagGen($type, $id, $value, $additionalAttr = '', $additionalText = '')
    {
        printf(
            '<input type="%s" id="%s" name="%s[%s]" value="%s" %s /> %s',
            $type,
            $id,
            $this->optionName,
            $id,
            $value,
            $additionalAttr,
            $additionalText
        );
    }

    public function multipleInputTagGen($type, $id, $value, $additionalAttr = '', $additionalText = '')
    {
        printf(
            '<input type="%s" id="%s-%s"  name="%s[%s][]" value="%s" %s /> %s',
            $type,
            $id,
            $value,
            $this->optionName,
            $id,
            $value,
            $additionalAttr,
            $additionalText
        );
    }


    public function sanitize($input, $settingFields)
    {
        $new_input = array();
        foreach ($settingFields as $key => $settingField) {
            if (isset($input[$key])) {
                switch ($settingField['type']) {
                    case 'text':
                        $new_input[$key] = sanitize_text_field($input[$key]);
                        break;
                    default:
                        $new_input[$key] = $input[$key];
                        break;
                }
            }
        }
        return $new_input;
    }

}

