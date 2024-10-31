<?php

class Roxi_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Roxi Settings', 
            'Roxi Settings', 
            'manage_options', 
            'roxi-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'roxi_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>My Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'roxi_option_group' );   
                do_settings_sections( 'roxi-setting-admin' );
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
        register_setting(
            'roxi_option_group', // Option group
            'roxi_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Widget Settings', // Title
            null,
            'roxi-setting-admin' // Page
        );

        add_settings_section(
            'display_section_id', // ID
            'Display Settings', // Title
            array( $this, 'print_display_section_info' ), // Callback
            'roxi-setting-admin' // Page
        );

        add_settings_field(
            'venue_id', // ID
            'Venue ID #', // Title 
            array( $this, 'venue_id_callback' ), // Callback
            'roxi-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'theme', // ID
            'Theme', // Title 
            array( $this, 'theme_callback' ), // Callback
            'roxi-setting-admin', // Page
            'display_section_id' // Section           
        );  

         add_settings_field(
            'style', // ID
            'Style', // Title 
            array( $this, 'style_callback' ), // Callback
            'roxi-setting-admin', // Page
            'display_section_id' // Section           
        ); 
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['venue_id'] ) )
            $new_input['venue_id'] = absint( $input['venue_id'] );

        if (isset($input['theme']))
        	$new_input['theme'] = sanitize_text_field($input['theme']);

        if (isset($input['style']))
        	$new_input['style'] = sanitize_text_field($input['style']);

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_display_section_info()
    {
        print 'Control the look and feel of your widget:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function venue_id_callback()
    {
        printf(
            '<p><input type="text" id="venue_id" name="roxi_options[venue_id]" value="%s" /></p><p>This is the Roxi ID of your venue. You can find your venue ID in the Roxi Dashboard.</p>',
            isset( $this->options['venue_id'] ) ? esc_attr( $this->options['venue_id']) : ''
        );
    }

    /**
     * Displays the radio buttons for choosing the theme
     */
    public function theme_callback()
    {
    	printf(
    		'<p>Choose which theme the widget should use.</p><p><label><input type="radio" id="theme_light" name="roxi_options[theme]" value="light" %s /> Light</label><br /><label><input type="radio" id="theme_transparent" name="roxi_options[theme]" value="transparent" %s /> Transparent</label><br /><label><input type="radio" id="theme_dark" name="roxi_options[theme]" value="dark" %s /> Dark</label></p>',
    		(isset($this->options['theme']) && $this->options['theme'] == 'light') || !isset($this->options['theme']) ? 'checked="checked"' : '',
    		isset($this->options['theme']) && $this->options['theme'] == 'transparent' ? 'checked="checked"' : '',
    		isset($this->options['theme']) && $this->options['theme'] == 'dark' ? 'checked="checked"' : ''
		);
    }

    /**
     * Displays the radio buttons for choosing the style
     */
    public function style_callback()
    {
    	printf(
    		'<p>Choose which style the events in the widget will appear as.</p><p><label><input type="radio" id="style_stub" name="roxi_options[style]" value="stub" %s /> Event Stub</label><br /><label><input type="radio" id="style_full" name="roxi_options[style]" value="full" %s /> Full Flyer</label></p>',
    		(isset($this->options['style']) && $this->options['style'] == 'stub') || !isset($this->options['style']) ? 'checked="checked"' : '',
    		isset($this->options['style']) && $this->options['style'] == 'full' ? 'checked="checked"' : ''
		);
    }
}

// if in admin, create the settings page
if( is_admin() )
    $my_settings_page = new Roxi_Settings();
