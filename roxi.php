<?php
/**
 * Plugin Name: Roxi
 * Plugin URL: http://getroxi.com
 * Description: This plugin allows the Roxi Widget to be placed on any post or page.
 * Version: 1.0.1
 * Author: Roxi Communications Inc.
 * Author URI: http://getroxi.com
 */

// include the settings admin page
include(plugin_dir_path( __FILE__ ) . 'class.roxi-settings.php');

// register 'roxi_options' to hold all options for this plugin
add_option('roxi_options', array('theme' => 'light', 'style' => 'stub'));

/*****************************
 * BEGIN Short tag handler
 ****************************/

/**
 * Listener for the [rox_widget] short tag. Will replace the tag with the full roxi widget code.
 * @param  array    $atts   Optional attributes that the short tag has
 * @return String           The roxi widget code.
 */
function roxi_func($atts)
{
    // get roxi options
	$options = get_option('roxi_options');

    // determine configured theme
	$theme = "";
	if (isset($options['theme']))
	{
		if ($options['theme'] == 'transparent')
			$theme = "_roxi.transparentTheme = true;";
		else if ($options['theme'] == 'dark')
			$theme = "_roxi.darkTheme = true;";
	}

    // determine configured style
	$style = "";
	if (isset($options['style']) && $options['style'] == 'full')
		$style = "full";

    // widget code
    if (!is_numeric($options['venue_id']) || $options['venue_id'] == 0)
    {
        $roxiWidget = "<p>The Roxi widget has not yet been setup. Please set your Venue ID in the \"Roxi Settings\" page of the WordPress admin.</p>";
    }
    else
    {
	   $roxiWidget = "<!-- BEGIN Roxi Widget --> <script type=\"text/javascript\"> var _roxi = {}; $theme _roxi._venueID = ".$options['venue_id']."; (function() { var roxi = document.createElement('script'); roxi.type = 'text/javascript'; roxi.async = true; roxi.src = 'https://getroxi.com/js/loader-widget.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(roxi, s); })(); </script> <div id=\"roxi-widget\" class=\"roxi $style\"></div> <!-- END Roxi Widget -->";
    }

	return $roxiWidget;
}

// register listener for short code
add_shortcode('roxi_widget', 'roxi_func');

/*****************************
 * END Short tag handler
 ****************************/

/*****************************
 * BEGIN Tinymce plugin to add a roxi button that inserts the short tag
 ****************************/

// init process for registering the roxi button in the editor
add_action('init', 'roxi_shortcode_button_init');
function roxi_shortcode_button_init() {

    // Abort early if the user will never see TinyMCE
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
        return;

    // add callback to register plugin
    add_filter("mce_external_plugins", "roxi_register_tinymce_plugin"); 

    // add callback to add button to tinymce toolbar
    add_filter('mce_buttons', 'roxi_add_tinymce_button');
}


// register the plugin with tinymce
function roxi_register_tinymce_plugin($plugin_array) {
    $plugin_array['roxi_plugin'] = plugins_url('/js/roxi-button-plugin.js',__file__);
    return $plugin_array;
}

// add the roxi button to the toolbar
function roxi_add_tinymce_button($buttons) {
            //Add the button ID to the $button array
    $buttons[] = "roxi_button";
    return $buttons;
}

/*****************************
 * END Tinymce plugin
 ****************************/

/*****************************
 * BEGIN Quicktag for the html editor
 ****************************/

// add a quicktag to the HTML (text) editor
function add_roxi_quicktag() {
    if (wp_script_is('quicktags')) { ?>
        <script type="text/javascript">
            QTags.addButton( 'roxi_button', 'roxi', '[roxi_widget]', null, null, 'Insert the Roxi Widget');
        </script>
	<?php }
}
add_action('admin_print_footer_scripts', 'add_roxi_quicktag');

/*****************************
 * END Quicktag
 ****************************/

/*****************************
 * START Add 'Settings' link to plugins page
 ****************************/

 // Add settings link on plugin page
function roxi_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=roxi-setting-admin">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'roxi_settings_link' );

/*****************************
 * END 'Settings' link
 ****************************/

?>