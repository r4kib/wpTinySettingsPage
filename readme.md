#WP Tiny settings Page
This helps to create simple settings page for WordPress Plugins / Themes.

## Getting Started
1. Download and include `wpTinySettingsPage.php` file in the plugin.
2. create a new instance of WPTinySettingsPage with following params-
    1. pageTitle string
    2. menuTitle string
    3. menuSlug string
    4. optionName string
    5. settings array maintain following structure-
          ``array('sectionSlug' => array(
                                      'title' => 'Section Title',
                                      'callback' => 'name of valid callbackfunction,
                                      'fields' => array(
                                      'fieldSlug1' => array()
                                      )
                                      )``

## Filed args by type
 1. *title* (string) [required] 
 2. *type* (string) [required]  possible value `checkboxes` 
 3. *options* (array) [required if type `checkboxes`]  follow format `array('value'=>'Title')`
 
 ## License
 MIT